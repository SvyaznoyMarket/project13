<?php

namespace EnterQuery;

trait CurlQueryTrait
{
    use \EnterApplication\CurlTrait;

    /**
     * @param $url
     * @param array $data
     * @param int|null $timeoutMultiplier
     * @param callable|null $callback
     * @param \Exception $error
     * @param callable|null $decoder
     * @return \EnterLab\Curl\Query
     */
    public function prepareCurlQuery(
        $url,
        $data = [],
        $timeoutMultiplier = null,
        $callback = null,
        \Exception &$error = null,
        $decoder = null
    ) {
        if ($timeoutMultiplier < 0) {
            throw new \InvalidArgumentException();
        }

        $timeout = \App::config()->coreV2['timeout'] * 1000; // таймаут, мс
        if (!$timeout || $timeout > 90000) {
            $timeout = 5000;
        }
        $timeout *= $timeoutMultiplier;

        $query = $this->getCurl()->createQuery();

        $startingResponse = false;
        $query->request->options = [
            CURLOPT_HEADER         => false,
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
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOSIGNAL       => true,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_ENCODING       => 'gzip,deflate',

            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT_MS     => $timeout,
            CURLOPT_HTTPHEADER     => ['X-Request-Id: ' . \App::$id, 'Expect:'], // TODO: customize
        ];
        if ($data) {
            $query->request->options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json'; // TODO: customize
            $query->request->options += [
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => json_encode($data),
            ];
        }
        $query->resolveCallback = function() use (&$query, &$result, &$callback, &$decoder, &$error) {
            if ($query->response->error) {
                $error = $query->response->error;

                return;
            }

            $result = null;
            if (is_callable($decoder)) {
                try {
                    $result = call_user_func($decoder, $query->response->body);
                } catch (\Exception $e) {
                    $error = $e;
                }
            } else {
                $result = $query->response->body;
            }

            if (!$error && is_callable($callback)) {
                call_user_func($callback);
            }
        };

        $this->getCurl()->addQuery($query);

        return $query;
    }
}