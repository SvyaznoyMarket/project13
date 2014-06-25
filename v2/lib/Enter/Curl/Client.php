<?php

namespace Enter\Curl;

use Enter\Logging\Logger;

/**
 * Class Client
 * @package Curl
 * @author Georgiy Lazukin <georgiy.lazukin@gmail.com>
 * @author Sergey Sapego <sapegosv@gmail.com>
 */
class Client {
    /** @var Logger */
    private $logger;
    /** @var resource */
    private $multiConnection;
    /** @var resource[] */
    private $connections = [];
    /** @var Query[] */
    private $queries = [];
    /** @var bool */
    private $stillExecuting = false;
    /** @var Config */
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger = null) {
        $this->logger = $logger;
    }

    public function __clone() {
        $this->multiConnection = null;
        $this->connections = [];
        $this->queries = [];
        $this->stillExecuting = false;
    }

    /**
     * @param Query $query
     * @return $this
     * @throws \Exception
     */
    public function query(Query $query) {
        // таймаут по умолчанию
        if (!$query->getTimeout()) {
            $query->setTimeout($this->config->timeout);
        }

        $query->incCall();

        $connection = $this->create($query);

        $query->setStartAt(microtime(true));

        $response = curl_exec($connection);
        try {
            $info = curl_getinfo($connection);
            $query->setInfo($info);

            if (curl_errno($connection) > 0) {
                throw new \RuntimeException(curl_error($connection), curl_errno($connection));
            }

            if ($info['http_code'] >= 300) {
                // TODO: обработка статуса
                $query->setError(new \Exception('Неверный статус ответа', $info['http_code']));
            }

            $headers = [];
            $this->parseResponse($connection, $response, $headers);
            $query->setHeaders($headers);

            curl_close($connection);

            if (null === $response) {
                throw new \Exception(sprintf('Пустой ответ от %s', $query->getUrl()));
            }

            $query->callback($response);
            $query->setEndAt(microtime(true));

            if ($this->logger) $this->logger->push(['action' => __METHOD__, 'query' => $query, 'tag' => ['curl']]);

            return $this;
        } catch (\Exception $e) {
            $query->setError($e);
            $query->setEndAt(microtime(true));

            if ($this->logger) $this->logger->push(['type' => 'error', 'action' => __METHOD__, 'query' => $query, 'tag' => ['curl']]);

            throw $e;
        }
    }

    /**
     * @param float|null $retryTimeout
     * @param int|null $retryCount
     * @return $this
     * @throws \Exception
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        if (!$this->multiConnection) {
            if ($this->logger) $this->logger->push(['type' => 'warn', 'action' => __METHOD__, 'message' => 'Нет запросов для выполнения', 'tag' => ['curl']]);

            return $this;
        }

        if (null === $retryTimeout) {
            $retryTimeout = $this->config->retryTimeout;
        }
        if (null === $retryCount) {
            $retryCount = $this->config->retryCount;
        }

        try {
            $absoluteTimeout = microtime(true);

            foreach ($this->queries as $query) {
                $query->setStartAt($absoluteTimeout);
            }

            do {
                if ($absoluteTimeout <= microtime(true)) {
                    $absoluteTimeout += $retryTimeout;
                }

                do {
                    $code = curl_multi_exec($this->multiConnection, $stillExecuting);
                    $this->stillExecuting = $stillExecuting;
                } while ($code == CURLM_CALL_MULTI_PERFORM);

                // if one or more descriptors is ready, read content and run callbacks
                while ($done = curl_multi_info_read($this->multiConnection)) {
                    $connection = $done['handle'];
                    $queryId = (string)$connection;

                    foreach ($this->queries[$queryId]->getConnections() as $resource) {
                        if ($resource !== $connection) {
                            curl_multi_remove_handle($this->multiConnection, $resource);
                        }
                    }

                    try {
                        $info = curl_getinfo($connection);
                        $this->queries[$queryId]->setInfo($info);

                        if (curl_errno($connection) > 0) {
                            throw new \RuntimeException(curl_error($connection), curl_errno($connection));
                        }
                        if ($info['http_code'] >= 300) {
                            // TODO: обработка статуса
                            $this->queries[$queryId]->setError(new \Exception('Неверный статус ответа', $info['http_code']));
                        }

                        $response = curl_multi_getcontent($connection);
                        if (null === $response) {
                            throw new \Exception(sprintf('Пустой ответ от %s', $this->queries[$queryId]->getUrl()));
                        }

                        $headers = [];
                        $this->parseResponse($connection, $response, $headers);
                        $this->queries[$queryId]->setHeaders($headers);

                        // TODO: отложенный запуск обработчиков
                        $this->queries[$queryId]->callback($response);

                        if ($this->logger) $this->logger->push(['action' => __METHOD__, 'query' => $this->queries[$queryId], 'tag' => ['curl']]);
                        $this->queries[$queryId]->setEndAt(microtime(true));

                        unset($this->queries[$queryId]);
                    } catch (\Exception $e) {
                        $this->queries[$queryId]->setError($e);
                        $this->queries[$queryId]->setEndAt(microtime(true));

                        if ($this->logger) $this->logger->push(['type' => 'error', 'action' => __METHOD__, 'query' => $this->queries[$queryId], 'tag' => ['curl']]);
                    }
                }

                if ($stillExecuting) {
                    $timeout = $absoluteTimeout - microtime(true);
                    if (0 >= $timeout) {
                        $timeout += $retryTimeout;
                        $absoluteTimeout += $retryTimeout;
                    }
                    $tryAvailable = false;
                    foreach ($this->queries as $query) {
                        if (count($query->getConnections()) < $retryCount) {
                            $tryAvailable = true;
                            break;
                        }
                    }
                    if ($tryAvailable && null !== $retryTimeout) {
                        $ready = curl_multi_select($this->multiConnection, $timeout);
                    } else {
                        $ready = curl_multi_select($this->multiConnection, 30);
                    }

                    if (0 === $ready) {
                        foreach ($this->queries as $query) {
                            if (count($query->getConnections()) >= $retryCount) continue;
                            $this->prepare($query);
                        }
                    }
                }
            } while ($this->stillExecuting);
        } catch (\Exception $e) {
            $this->clear();

            if ($this->logger) $this->logger->push(['type' => 'error', 'action' => __METHOD__, 'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()], 'tag' => ['curl']]);

            throw $e;
        }

        $this->clear();

        return $this;
    }

    public function clear() {
        foreach ($this->connections as $resource) {
            curl_multi_remove_handle($this->multiConnection, $resource);
        }
        curl_multi_close($this->multiConnection);
        $this->multiConnection = null;
        $this->connections = [];
        $this->queries = [];

        return $this;
    }

    /**
     * @param Query $query
     * @return $this
     * @throws \Exception
     */
    public function prepare(Query $query) {
        $query->incCall();

        if (!$this->multiConnection) {
            $this->multiConnection = curl_multi_init();
        }

        $resource = $this->create($query);
        if (0 !== curl_multi_add_handle($this->multiConnection, $resource)) {
            $message = curl_error($resource);
            if ($this->logger) $this->logger->push(['type' => 'error', 'action' => __METHOD__, 'message' => $message, 'tag' => ['curl']]);

            throw new \Exception($message);
        };
        $this->connections[] = $resource;

        $this->stillExecuting = true;

        $this->queries[$query->getId()] = $query;

        return $this;
    }

    /**
     * @param Query $query
     * @return resource
     */
    private function create(Query $query) {

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, true);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($connection, CURLOPT_URL, $query->getUrl());
        if ((bool)$this->config->httpheader) {
            curl_setopt($connection, CURLOPT_HTTPHEADER, $this->config->httpheader);
        }
        if ($this->config->encoding) {
            curl_setopt($connection, CURLOPT_ENCODING, $this->config->encoding);
        }

        if ($query->getTimeout()) {
            curl_setopt($connection, CURLOPT_NOSIGNAL, true);
            curl_setopt($connection, CURLOPT_TIMEOUT_MS, $query->getTimeout() * 1000);
        }

        if ($query->getAuth()) {
            curl_setopt($connection, CURLOPT_USERPWD, $query->getAuth());
        }

        if ((bool)$query->getData()) {
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($query->getData()));
        }

        if ($this->config->referer) {
            curl_setopt($connection, CURLOPT_REFERER, $this->config->referer);
        }

        $query->setId((string)$connection);
        $query->addConnection($connection);

        return $connection;
    }

    /**
     * @param resource $connection
     * @param string $response
     * @param array|null $headers
     */
    private function parseResponse($connection, &$response, &$headers = null) {
        $size = curl_getinfo($connection, CURLINFO_HEADER_SIZE);

        if (is_array($headers)) {
            foreach (explode("\r\n", mb_substr($response, 0, $size)) as $line) {
                if ($pos = strpos($line, ':')) {
                    $key = substr($line, 0, $pos);
                    $value = trim(substr($line, $pos + 1));
                    $headers[$key] = $value;
                } else {
                    $headers[] = $line;
                }
            }
        }

        $response = mb_substr($response, $size);
    }
}