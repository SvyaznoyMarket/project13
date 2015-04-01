<?php

namespace SprosiKupi;

class Client {
    private $config;
    private $logger;

    public function __construct(array $config, \Logger\LoggerInterface $logger = null) {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function query($productId) {
        $startedAt = \Debug\Timer::start('SprosiKupi');

        $connection = $this->createResource($productId);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new Exception(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);

            //$this->logger->debug('SprosiKupi response resource: ' . $connection, ['SprosiKupi']);

            if ($this->config['logEnabled']) {
                $this->logger->info('Response ' . $connection . ' : ' . (is_array($info) ? json_encode($info, JSON_UNESCAPED_UNICODE) : $info), ['SprosiKupi']);
            }

            if ($info['http_code'] >= 300) {
                throw new Exception(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
            }

            curl_close($connection);

            $spend = \Debug\Timer::stop('SprosiKupi');
            \App::logger()->info('End SprosiKupi in ' . $spend, ['SprosiKupi']);

            \App::logger()->info([
                'message' => 'End curl',
                'url'     => $info['url'],
                'data'    => [],
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'timeout' => $this->config['timeout'],
                'spend'   => $spend,
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
            ], ['curl', 'SprosiKupi']);

            return $response;

        } catch (Exception $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('SprosiKupi');

            \App::logger()->error([
                'message' => 'Fail curl',
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                'url'     => $this->getUrl($productId),
                'data'    => [],
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'response' => isset($response) ? $response : null,
                'timeout' => $this->config['timeout'],
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
                'spend'   => $spend,
            ], ['curl', 'SprosiKupi']);
        }
    }

    private function createResource($productId) {
        $query = $this->getUrl($productId);

        \App::logger()->info('Start SprosiKupi query: ' . $query, ['SprosiKupi']);

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 0);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_NOSIGNAL, 1);
        curl_setopt($connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($connection, CURLOPT_TIMEOUT_MS, $this->config['timeout']);
        curl_setopt($connection, CURLOPT_URL, $query);

        if ($this->config['logEnabled']) {
            $this->logger->info('Send SprosiKupi request ' . $connection, ['SprosiKupi']);
        }

        return $connection;
    }

    private function getUrl($productId) {
        return 'http://widget.sprosikupi.ru/seo/15898ce50992cc789cdc21/' . $productId;
    }
}