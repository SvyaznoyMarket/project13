<?php

namespace EnterQuery;

trait CurlQueryTrait
{
    use \EnterApplication\CurlTrait, JsonTrait;

    /**
     * @param string $prefix
     * @param string $action
     * @param string array $query
     * @return string
     */
    public function buildUrl($prefix, $action, $query = [])
    {
        return $prefix . $action . ($query ? http_build_query($query) : '');
    }


    public function pushCurlQuery(
        $url,
        $data = [],
        $timeout = null,
        $callback = null,
        \Exception &$error = null,
        $decoder = null
    ) {
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
        ];
        if ($data) {
            $query->request->options += [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
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