<?php

namespace EnterQuery;

use EnterLab\Curl\Query;

trait CurlQueryTrait
{
    use \EnterApplication\CurlTrait;
    use QueryCacheTrait;

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
     * @param int|null $timeoutMultiplier
     * @param \Exception $error
     * @param callable|null $decoder
     * @return \EnterLab\Curl\Query[]
     */
    public function prepareCurlQuery(
        $url,
        $data = [],
        $timeoutMultiplier = null,
        \Exception &$error = null,
        $decoder = null
    ) {
        if ($timeoutMultiplier < 0) {
            throw new \InvalidArgumentException();
        }

        $timeout = \App::config()->coreV2['timeout'] * 1000; // таймаут, мс
        $timeout *= $timeoutMultiplier;
        // ограничение таймаута
        if (!$timeout || $timeout > 90000) {
            $timeout = 5000;
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
        ], ['curl']);
        // end

        $query = $this->getCurl()->createQuery();

        $startingResponse = false;
        $query->request->options = [
            CURLOPT_HEADER            => false,
            CURLOPT_HEADERFUNCTION    => function ($ch, $h) use (&$query, &$startingResponse) {
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
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_NOSIGNAL          => true,
            CURLOPT_IPRESOLVE         => CURL_IPRESOLVE_V4,
            CURLOPT_ENCODING          => 'gzip,deflate',

            CURLOPT_URL               => $url,
            CURLOPT_TIMEOUT_MS        => $timeout,
            CURLOPT_CONNECTTIMEOUT_MS => $timeout,
            CURLOPT_HTTPHEADER        => ['X-Request-Id: ' . \App::$id, 'Expect:'], // TODO: customize
        ];
        if ($data) {
            $query->request->options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json'; // TODO: customize
            $query->request->options += [
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => json_encode($data),
            ];
        }
        $query->resolveCallback = function() use (
            &$query,
            &$result,
            &$decoder,
            &$error,
            &$data,
            &$startedAt
        ) {
            if ($query->response->error) {
                $error = $query->response->error;

                // TODO: удалить; сейчас нужно для старого журнала
                \App::logger()->error([
                    'message'      => 'Fail curl',
                    'cache'        => true, // важно
                    'delay'        => $query->request->delay, // важно
                    'error'        => ['code' => $error->getCode(), 'message' => $error->getMessage()],
                    'url'          => $query->request->options[CURLOPT_URL],
                    'data'         => $data,
                    'info'         => $query->response->info,
                    'header'       => null,
                    'response'     => $query->response->body,
                    'retryTimeout' => null,
                    'retryCount'   => null,
                    'timeout'      => $query->request->options[CURLOPT_TIMEOUT_MS],
                    'startAt'      => $startedAt,
                    'endAt'        => microtime(true),
                ], ['curl']);
                // end

                return;
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
                        'cache'   => true, // важно
                        'delay'   => $query->request->delay, // важно
                        'url'     => $query->request->options[CURLOPT_URL],
                        'data'    => $data,
                        'info'    => $query->response->info,
                        'header'  => $headers,
                        'timeout' => $query->request->options[CURLOPT_TIMEOUT_MS],
                        'startAt' => $startedAt,
                        'endAt'   => $endAt,
                        'spend'   => $endAt - $startedAt,
                    ], ['curl']);
                    // end
                } catch (\Exception $e) {
                    $error = $e;
                }

                $id = $this->getQueryCacheId($query->request->options[CURLOPT_URL], $data);
                $this->setQueryCache($id, $result);
            } else {
                $result = $query->response->body;
            }

            if (!$error) {
                foreach ($this->callbacks as $callback) {
                    try {
                        call_user_func($callback->handler);
                    } catch(\Exception $e) {
                        $callback->error = $e;
                    }
                }
            }
        };

        // retry
        // TODO: customize
        /** @var Query[] $queries */
        $queries = [$query];
        $retryQuery = clone $query;
        $retryQuery->request->delay = $timeout / 2;
        $queries[] = $retryQuery;

        foreach ($queries as $query) {
            $this->addCallback(function() use (&$queries, &$query) {
                var_dump(round(explode(' ', microtime())[0] * 10000) . ' remove duplicates');
                foreach ($queries as $iQuery) {
                    if ($query === $iQuery) {
                        var_dump(round(explode(' ', microtime())[0] * 10000) . ' it\'s me ' . $query->request);
                        continue;
                    }
                    if (!is_callable($iQuery->rejectCallback)) continue;

                    call_user_func($iQuery->rejectCallback); // отменяет запрос

                    $iQuery->rejectCallback = null;
                    $iQuery->resolveCallback = null;
                }
            });
        }

        // подготовка запросов
        foreach ($queries as $query) {
            $this->getCurl()->addQuery($query);
        }

        return $queries;
    }
}