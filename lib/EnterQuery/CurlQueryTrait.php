<?php

namespace EnterQuery;

use EnterLab\Curl\Query;

trait CurlQueryTrait
{
    use \EnterApplication\CurlTrait;
    use QueryCacheTrait;

    /**
     * @var \Exception|null
     */
    public $error;

    /**
     * @var Callback[]
     */
    protected $callbacks = [];

    /**
     * @param callable $handler
     * @return Callback
     */
    public function addCallback($handler)
    {
        if (!is_callable($handler)) {
            throw new \InvalidArgumentException('Неверный обработчик');
        }

        $callback = new Callback();
        $callback->handler = $handler;

        $this->callbacks[] = $callback;

        return $callback;
    }

    /**
     * @param $url
     * @param array $data
     * @param callable|null $decoder
     * @param int|null $timeoutRatio
     * @param float[] $delayRatios
     * @return Query[]
     */
    public function prepareCurlQuery(
        $url,
        $data = [],
        $decoder = null,
        $timeoutRatio = 1,
        array $delayRatios = null
    )
    {
        if ($timeoutRatio <= 0) {
            throw new \InvalidArgumentException(sprintf('Неверный коэффициент таймаута %s', $timeoutRatio));
        }

        $timeout = \App::config()->coreV2['timeout'] * 1000; // таймаут, мс
        $timeout = intval($timeout * $timeoutRatio);
        // ограничение таймаута
        if (!$timeout || $timeout > 90000) {
            $timeout = 5000;
        }

        // задержки по умолчанию
        if (null === $delayRatios) {
            $delayRatios = \App::config()->curlCache['delayRatio'] ?: [0];
        }

        // если таймаут слишком маленький, то убираем retry
        if ($timeout <= 1) {
            $delayRatios = [0];
        }

        // TODO: удалить; сейчас нужно для старого журнала
        $startedAt = microtime(true);
        \App::logger()->info([
            'message' => 'Create curl',
            'cache'   => true, // важно
            'url'     => $url,
            'data'    => $data,
            'timeout' => $timeout,
            'startAt' => $startedAt,
            'delays'  => array_map(function($ratio) use ($timeout) { return (int)($ratio * $timeout); }, $delayRatios),
        ], ['curl']);
        // end

        $queryCollection = new \ArrayObject();
        foreach ($delayRatios as $delayRatio) {
            $delay = (int)($timeout * $delayRatio);

            $query = $this->createCurlQuery(
                $url,
                $data,
                $timeout,
                $delay
            );

            $query->resolveCallback = function () use (
                $query,
                $queryCollection,
                &$result,
                $decoder,
                $data,
                $startedAt
            ) {
                // если ошибка
                if ($query->response->error) {
                    $this->error = $query->response->error;

                    // TODO: удалить; сейчас нужно для старого журнала
                    \App::logger()->error([
                        'message' => 'Fail curl',
                        'cache' => true, // важно
                        'delay' => $query->request->delay, // важно
                        'error' => ['code' => $this->error->getCode(), 'message' => $this->error->getMessage()],
                        'url' => $query->request->options[CURLOPT_URL],
                        'data' => $data,
                        'info' => $query->response->info,
                        'header' => null,
                        'response' => $query->response->body,
                        'retryTimeout' => null,
                        'retryCount' => null,
                        'timeout' => $query->request->options[CURLOPT_TIMEOUT_MS],
                        'startAt' => $startedAt,
                        'endAt' => microtime(true),
                    ], ['curl']);
                    // end

                    return;
                }

                // если ошибки нет, то удалить раннее полученную ошибку, если есть (например, первая попытка сорвалась по таймауту)
                if ($this->error) {
                    $this->error = null;
                }

                // удалить дубликаты
                foreach ($queryCollection as $retryQuery) {
                    if (is_callable($retryQuery->rejectCallback)) {
                        call_user_func($retryQuery->rejectCallback); // отменяет запрос
                    }

                    //$retryQuery->resolveCallback = null;
                    unset($retryQuery);
                }

                $result = null;
                if (is_callable($decoder)) {
                    try {
                        $result = call_user_func($decoder, $query->response->body, $query->response->statusCode);

                        // TODO: удалить; сейчас нужно для старого журнала
                        $endAt = microtime(true);
                        $headers = [];
                        foreach ($query->response->headers as $header) {
                            if ($pos = strpos($header, ':')) {
                                $key = substr($header, 0, $pos);
                                $value = trim(substr($header, $pos + 1));
                                $headers[$key] = $value;
                            } else {
                                $headers[] = $header;
                            }
                        }
                        \App::logger()->info([
                            'message' => 'End curl',
                            'cache' => true, // важно
                            'delay' => $query->request->delay, // важно
                            'url' => $query->request->options[CURLOPT_URL],
                            'data' => $data,
                            'info' => $query->response->info,
                            'header' => $headers,
                            'timeout' => $query->request->options[CURLOPT_TIMEOUT_MS],
                            'startAt' => $startedAt,
                            'endAt' => $endAt,
                            'spend' => $endAt - $startedAt,
                        ], ['curl']);
                        // end
                    } catch (\Exception $error) {
                        $this->error = $error;
                    }

                    $id = $this->getQueryCacheId($query->request->options[CURLOPT_URL], $data);
                    $this->setQueryCache($id, $result);
                } else {
                    $result = $query->response->body;
                }

                if (!$this->error) {
                    foreach ($this->callbacks as $callback) {
                        try {
                            call_user_func($callback->handler);
                        } catch (\Exception $error) {
                            $callback->error = $error;
                        }
                    }
                }

                $query->__destruct();
            };

            $queryCollection->append($query);
        }

        // подготовка запросов
        foreach ($queryCollection as $query) {
            $this->getCurl()->addQuery($query);
        }

        return $queryCollection;
    }

    /**
     * @param string $url
     * @param string $data
     * @param int $timeout
     * @param int|null $delay
     * @return Query
     */
    private function createCurlQuery($url, $data, $timeout, $delay)
    {
        $query = $this->getCurl()->createQuery();

        $query->request->delay = $delay;

        $startingResponse = false;
        $query->request->options = [
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => false, // важно
            CURLOPT_HEADERFUNCTION => function ($ch, $h) use (&$query, &$startingResponse) {
                $value = trim($h);
                if ($value === '') {
                    $startingResponse = true;
                } elseif ($startingResponse) {
                    $startingResponse = false;
                    $query->response->headers = [$value];
                } else {
                    $query->response->headers[] = $value;
                }

                return strlen($h);
            },
            CURLOPT_WRITEFUNCTION => function($ch, $str) use (&$query) {
                //var_dump((string)$query->request);
                $query->response->body .= $str;

                return strlen($str);
            },
            CURLOPT_NOSIGNAL => true,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0, // для решения проблемы {"code":56,"message":"Problem (2) in the Chunked-Encoded data"}
            CURLOPT_ENCODING => 'gzip,deflate',

            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT_MS => $timeout,
            CURLOPT_CONNECTTIMEOUT_MS => $timeout,
            CURLOPT_HTTPHEADER => ['X-Request-Id: ' . \App::$id, 'Expect:'], // TODO: customize
        ];
        if ($data) {
            $query->request->options[CURLOPT_POST] = true;
            $query->request->options[CURLOPT_POSTFIELDS] = json_encode($data); // TODO: customize
            $query->request->options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json'; // TODO: customize
        }

        return $query;
    }
}