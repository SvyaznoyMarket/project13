<?php

namespace Curl;

class Client {
    /** @var \Logger\LoggerInterface */
    private $logger;
    /** @var resource|null */
    private $multiHandler;
    /** @var array */
    private $successCallbacks = [];
    /** @var array */
    private $failCallbacks = [];
    /** @var array */
    private $resources = [];
    /** @var array */
    private $queries = [];
    /** @var array */
    private $queryIndex = [];
    /** @var bool */
    private $stillExecuting = false;
	/** @var bool активация нативного POST запроса */
	private $nativePost = false;

    /**
     * @param \Logger\LoggerInterface $logger
     */
    public function __construct(\Logger\LoggerInterface $logger) {
        $this->logger = $logger;
        $this->stillExecuting = false;
    }

    public function __clone() {
        $this->multiHandler = null;
        $this->successCallbacks = [];
        $this->failCallbacks = [];
        $this->resources = [];
        $this->queries = [];
        $this->queryIndex = [];
        $this->stillExecuting = false;
    }
	
	
	public function setNativePost($val=true) {
		$this->nativePost = true;
	}

    /**
     * @param string $url
     * @param array $data
     * @param float|null $timeout
     * @throws \RuntimeException
     * @throws \Exception|\RuntimeException
     * @return mixed
     */
    public function query($url, array $data = [], $timeout = null) {
        $startedAt = \Debug\Timer::start('curl');

        $timeout = $timeout ? $timeout : $this->getDefaultTimeout();
        $connection = $this->create($url, $data, $timeout);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new \RuntimeException(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);

            if ($info['http_code'] >= 300) {
                throw new \RuntimeException('Invalid http code: ' . $info['http_code'], (int)$info['http_code']);
            }

            if (null === $response) {
                throw new \RuntimeException(sprintf('Пустой ответ от %s %s', $info['url'], http_build_query($data)));
            }
            $header = [];
            $this->parseResponse($connection, $response, $header);

			$decodedResponse = $this->decode($response);
            curl_close($connection);

            $spend = \Debug\Timer::stop('curl');
            $this->logger->info([
                'message' => 'End curl',
                'url'     => $url,
                'data'    => $data,
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'timeout' => $timeout,
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
                'spend'   => $spend,
            ], ['curl']);

            return $decodedResponse;
        } catch (\RuntimeException $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('curl');

            $this->logger->error([
                'message' => 'Fail curl',
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                'url'     => $url,
                'data'    => $data,
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'resonse' => mb_substr($response, 0, 512),
                'timeout' => $timeout,
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
                'spend'   => $spend,
            ], ['curl']);

            \App::exception()->add($e);

            throw $e;
        }
    }

    /**
     * @param string        $url
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($url, array $data = [], $successCallback = null, $failCallback = null, $timeout = null) {
        $timeout = $timeout ? $timeout : $this->getDefaultTimeout();

        if (!$this->multiHandler) {
            $this->multiHandler = curl_multi_init();
        }
        $resource = $this->create($url, $data, $timeout);
        if (0 !== curl_multi_add_handle($this->multiHandler, $resource)) {
            $this->logger->error(['message' => 'Fail query', 'error'   => curl_error($resource),], ['curl']);

            return false;
        };
        $this->successCallbacks[(string)$resource] = $successCallback ?: function(){};
        $this->failCallbacks[(string)$resource] = $failCallback;
        $this->resources[] = $resource;

        /* нужно сохранить исходные данные для retry */
        $hash = md5($url . ':' . serialize($data));
        if (!isset($this->queries[$hash])) {
            $this->queries[$hash] = [
                'resources' => [
                    $resource,
                ],
                'query' => [
                    'url'     => $url,
                    'data'    => $data,
                    'timeout' => $timeout,
                ],
            ];
        } else {
            $this->queries[$hash]['resources'][] = $resource;
        }
        $this->queryIndex[(string)$resource] = $hash;
        $this->stillExecuting = true;

        return true;
    }

    /**
     * @param float|null $retryTimeout
     * @param int $retryCount
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function execute($retryTimeout = null, $retryCount = 0) {
        $startedAt = \Debug\Timer::start('curl');
        if (!$this->multiHandler) {
            $this->logger->warn(['message' => 'No query to execute'], ['curl']);
            return;
        }

        $this->logger->info(['message' => 'Curl execute', 'query.count' => count($this->queries)]);

        $error = null;
        try {
            $absoluteTimeout = microtime(true);
            do {
                if ($absoluteTimeout <= microtime(true)) {
                    $absoluteTimeout += $retryTimeout;
                    //$this->logger->debug(microtime(true) . ': Слудеющий таймаут должен сработать в ' . $absoluteTimeout, ['curl']);
                }
                do {
                    $code = curl_multi_exec($this->multiHandler, $stillExecuting);
                    $this->stillExecuting = $stillExecuting;
                } while ($code == CURLM_CALL_MULTI_PERFORM);

                // if one or more descriptors is ready, read content and run callbacks
                while ($done = curl_multi_info_read($this->multiHandler)) {
                    //$this->logger->debug('Curl response done: ' . print_r($done, 1), ['curl']);
                    $handler = $done['handle'];

                    //$this->logger->info(microtime(true) . ': получен ответ на запрос ' . $this->queries[$this->queryIndex[(string)$handler]]['query']['url'] . '[' . (string)$handler . ']');
                    //$this->logger->debug(microtime(true) . ': <- [' . (string)$handler . ']', ['curl']);

                    //удаляем запрос из массива запросов на исполнение и прерываем дублирующие запросы
                    foreach ($this->queries[$this->queryIndex[(string)$handler]]['resources'] as $resource) {
                        if (is_resource($resource) && ($resource !== $handler)) {
                            curl_multi_remove_handle($this->multiHandler, $resource);
                            curl_close($resource);
                        }
                    }

                    $info = curl_getinfo($handler);
                    //$this->logger->debug('Curl response resource: ' . $handler, ['curl']);
                    //$this->logger->debug('Curl response info: ' . $this->encodeInfo($info), ['curl']);

                    try {
                        if (curl_errno($handler) > 0) {
                            throw new \RuntimeException(curl_error($handler), curl_errno($handler));
                        }

                        $content = curl_multi_getcontent($handler);
                        $header = [];
                        $this->parseResponse($handler, $content, $header);

                        if (null === $content) {
                            throw new \RuntimeException(sprintf('Пустой ответ от %s %s', $info['url'], http_build_query($this->queries[$this->queryIndex[(string)$handler]]['query']['data'])));
                        }

                        if ($info['http_code'] >= 300) {
                            throw new \RuntimeException('Invalid http code ' . $info['http_code'], (int)$info['http_code']);
                        }

                        $decodedResponse = $this->decode($content);

                        $callback = $this->successCallbacks[(string)$handler];
                        if (is_callable($callback)) {
                            $callback($decodedResponse, (int)$handler);
                        } else {
                            $this->logger->error(sprintf('Неверная функция %s для %s', gettype($callback), $info['url']), ['curl']);
                        }

                        $this->logger->info([
                            'message'      => 'End curl',
                            'url'          => isset($info['url']) ? $info['url'] : null,
                            'data'         => isset($this->queries[$this->queryIndex[(string)$handler]]['query']['data']) ? $this->queries[$this->queryIndex[(string)$handler]]['query']['data'] : [],
                            'info'         => isset($info) ? $info : null,
                            'header'       => isset($header) ? $header : null,
                            //'response'      => isset($content) ? $content : null,
                            'retryTimeout' => $retryTimeout,
                            'retryCount'   => $retryCount,
                            'timeout'      => isset($this->queries[$this->queryIndex[(string)$handler]]['query']['timeout']) ? $this->queries[$this->queryIndex[(string)$handler]]['query']['timeout'] : null,
                            'startAt'      => $startedAt,
                            'endAt'        => microtime(true),
                        ], ['curl']);

                        if (isset($this->queries[$this->queryIndex[(string)$handler]])) {
                            if (is_resource($handler)) {
                                curl_multi_remove_handle($this->multiHandler, $handler);
                                curl_close($handler);
                            }
                            unset($this->queries[$this->queryIndex[(string)$handler]]);
                        }
                    } catch (\Exception $e) {
                        \App::exception()->add($e);

                        $this->logger->error([
                            'message'      => 'Fail curl',
                            'error'        => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                            'url'          => isset($info['url']) ? $info['url'] : null,
                            'data'         => isset($this->queries[$this->queryIndex[(string)$handler]]['query']['data']) ? $this->queries[$this->queryIndex[(string)$handler]]['query']['data'] : [],
                            'info'         => isset($info) ? $info : null,
                            'header'       => isset($header) ? $header : null,
                            'response'      => isset($content) ? mb_substr($content, 0, 512) : null,
                            'retryTimeout' => $retryTimeout,
                            'retryCount'   => $retryCount,
                            'timeout'      => isset($this->queries[$this->queryIndex[(string)$handler]]['query']['timeout']) ? $this->queries[$this->queryIndex[(string)$handler]]['query']['timeout'] : null,
                            'startAt'      => $startedAt,
                            'endAt'        => microtime(true),
                        ], ['curl']);

                        $callback = $this->failCallbacks[(string)$handler];
                        if ($callback) {
                            $callback($e, (int)$handler);
                        }

                        if (isset($this->queries[$this->queryIndex[(string)$handler]])) {
                            if (is_resource($handler)) {
                                curl_multi_remove_handle($this->multiHandler, $handler);
                                curl_close($handler);
                            }
                            unset($this->queries[$this->queryIndex[(string)$handler]]);
                        }
                    }
                }
                if ($stillExecuting) {
                    $timeout = $absoluteTimeout - microtime(true);
                    if (0 >= $timeout) {
                        $timeout += $retryTimeout;
                        $absoluteTimeout += $retryTimeout;
                    }
                    $isTryAvailable = false;
                    foreach ($this->queries as $query) {
                        if (count($query['resources']) < $retryCount) {
                            $isTryAvailable = true;
                            break;
                        }
                    }
                    if ($isTryAvailable && 0 !== $retryTimeout) {
                        //$this->logger->debug(microtime(true) . ': ждем ответа или ' . $timeout . ' сек', ['curl']);
                        $ready = curl_multi_select($this->multiHandler, $timeout);
                    } else {
                        //$this->logger->debug(microtime(true) . ':' . (0 === $retryTimeout ? '' : ' все попытки исчерпаны,') . ' ждем ответа', ['curl']);
                        $ready = curl_multi_select($this->multiHandler, $timeout);
                    }

                    if (0 === $ready) {
                        //Если случился timeout, то посылаем в ядро еще запросы
                        //$this->logger->debug(microtime(true) . ': произошло прерывание по таймауту, в очереди ' . count($this->queries) . ' запроса(ов)', ['curl']);

                        foreach ($this->queries as $query) {
                            if (count($query['resources']) >= $retryCount) continue;
                            //$this->logger->debug(microtime(true) . ': посылаю еще один запрос в ядро: ' . $query['query']['url'], ['curl']);
                            $this->logger->info([
                                'message' => 'Query retry',
                                'url'     => $query['query']['url'],
                                'data'    => $query['query']['data'],
                            ], ['curl']);
                            $this->addQuery(
                                $query['query']['url'],
                                $query['query']['data'],
                                $this->successCallbacks[(string)$query['resources'][0]],
                                isset($this->failCallbacks[(string)$query['resources'][0]]) ? $this->failCallbacks[(string)$query['resources'][0]] : null,
                                isset($query['query']['timeout']) ? $query['query']['timeout'] : null
                            );
                        }
                    }
                }
            } while ($this->stillExecuting);
        } catch (\Exception $e) {
            $error = $e;
        }
        // clear multi container
        foreach ($this->resources as $resource) {
            if (is_resource($resource)) {
                curl_multi_remove_handle($this->multiHandler, $resource);
                curl_close($resource);
            }
        }
        curl_multi_close($this->multiHandler);
        $this->multiHandler = null;
        $this->successCallbacks = [];
        $this->failCallbacks = [];
        $this->resources = [];
        if (!is_null($error)) {
            \App::exception()->add($error);
            $this->logger->error(['message' => 'Curl response', 'response' => isset($content) ? mb_substr($content, 0, 512) : null], ['curl']);
        }

        $spend = \Debug\Timer::stop('curl');

        $this->logger->info([
            'message'      => 'End curl executing',
            'retryTimeout' => $retryTimeout,
            'retryCount'   => $retryCount,
            'startAt'      => $startedAt,
            'endAt'        => microtime(true),
            'spend'        => $spend,
        ], ['curl']);
    }

    /**
     * @param string     $url
     * @param array      $data
     * @param float|null $timeout
     * @return resource
     */
    protected function create($url, array $data = [], $timeout = null) {
        $timeout = $timeout ? $timeout : $this->getDefaultTimeout();

        $this->logger->info([
            'message' => 'Create curl',
            'url'     => $url,
            'data'    => $data,
            'timeout' => $timeout,
            'startAt' => microtime(true),
        ], ['curl']);

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 1);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, ['X-Request-Id: ' . \App::$id, 'Expect:']);
        curl_setopt($connection, CURLOPT_ENCODING, 'gzip,deflate');

        if(isset($data['http_user']) && isset($data['http_password'])) {
            curl_setopt($connection, CURLOPT_USERPWD, $data['http_user'].":".$data['http_password']);
            unset($data['http_user']);
            unset($data['http_password']);
        }

        if ($timeout) {
            curl_setopt($connection, CURLOPT_NOSIGNAL, 1);
            curl_setopt($connection, CURLOPT_TIMEOUT_MS, $timeout * 1000);
            curl_setopt($connection, CURLOPT_CONNECTTIMEOUT_MS, $timeout * 1000);
        }

		if (!$this->nativePost && (bool)$data) {
            curl_setopt($connection, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($this->nativePost && (bool)$data) {
			
			/**
			 * The usage of the @filename API for file uploading is deprecated for php >=5.5
			 * Но мы поддержим, т.к. не очень понимаю как тут нормально добавить эту фичу пишу тут
			 */
			if((float)PHP_VERSION>=5.5) {
				foreach($data as $k => $v) {
					if($v[0] !=='@')
						continue;
					
					$data[$k] = $this->initPostFile($v);
				}
			}
			
			curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
		}

        if ($referer = \App::config()->mainHost) {
            curl_setopt($connection, CURLOPT_REFERER, $referer);
        }

        return $connection;
    }
	
	
	private function initPostFile($curlString) {
		$fileParams  = [
			'type' => null,
			'filename' => null
		];

		// не силен в регулярках, не осилил в одну строчку
		$t = explode(';',$curlString);
		$file = substr($t[0], 1);
		
		foreach($t as $vv) {
			$tt = explode('=',$vv);
			$fileParams[$tt[0]] = $tt[1];
		}
		
		return curl_file_create($file, $fileParams['type'], $fileParams['type']);
	}

    /**
     * @param $connection
     * @param $response
     * @param null $headers
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

    /**
     * @param string $response Тело ответа без заголовка (header)
     * @throws \RuntimeException
     * @throws Exception
     * @return mixed
     */
    protected function decode($response) {
        if (is_null($response)) {
            throw new \RuntimeException('Пустой ответ');
        }

        $decoded = json_decode($response, true);
        if ($code = json_last_error()) {
            switch ($code) {
                case JSON_ERROR_DEPTH:
                    $message = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $message = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $message = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $message = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $message = 'Unknown error';
                    break;
            }

            throw new \RuntimeException($message, $code);
        }

        if (is_array($decoded)) {
            if (array_key_exists('error', $decoded)) {
                $e = new Exception(((isset($decoded['error']['message']) && is_scalar($decoded['error']['message'])) ? $decoded['error']['message'] : 'В ответе содержится ошибка'), (int)$decoded['error']['code']);

                /**
                 * $e->setContent нужен для того, чтобы сохранять ошибки от /v2/order/calc-tmp:
                 *   кроме error.code и error.message возвращается массив error.product_error_list
                 */
                $e->setContent($decoded['error']);

                throw $e;
            }

            if (array_key_exists('result', $decoded)) {
                $decoded = $decoded['result'];
            }
        }

        return $decoded;
    }

    /**
     * @return int
     */
    private function getDefaultTimeout() {
        return \App::config()->coreV2['timeout'];
    }
}