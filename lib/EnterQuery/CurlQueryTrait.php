<?php

namespace EnterQuery;

trait CurlQueryTrait
{
    use \EnterApplication\CurlTrait;
    use QueryCacheTrait;

    /**
     * @param $url
     * @param array $data
     * @param int|null $timeoutMultiplier
     * @param callable[] $callbacks
     * @param \Exception $error
     * @param callable|null $decoder
     * @return \EnterLab\Curl\Query
     */
    public function prepareCurlQuery(
        $url,
        $data = [],
        $timeoutMultiplier = null,
        array $callbacks = [],
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
            &$callbacks,
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

                    $id = $this->getQueryCacheId($query->request->options[CURLOPT_URL], $data);
                    $this->setQueryCache($id, $result);

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
            } else {
                $result = $query->response->body;
            }

            if (!$error) {
                foreach ($callbacks as $callback) {
                    if (!is_callable($callback)) {
                        \App::logger()->error(['error' => sprintf('Неверная функция обратного вызова для %s', $query->request->options[CURLOPT_URL]), 'sender' => __FILE__ . ' ' .  __LINE__], ['curl-cache']);

                        continue;
                    }

                    $callbackError = null;
                    try {
                        call_user_func($callback);
                    } catch(\Exception $e) {
                        $callbackError = $e; // TODO: подумать как передать ошибку обратного вызова
                    }
                }
            }
        };

        $this->getCurl()->addQuery($query);

        return $query;
    }
}