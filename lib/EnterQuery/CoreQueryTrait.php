<?php

namespace EnterQuery;

use EnterLab\Curl;

trait CoreQueryTrait
{
    use JsonTrait, \EnterLab\Application\CurlTrait;

    /**
     * @param string $path
     * @param array $queryParams
     * @param array $data
     * @param int $timeoutMultiplier
     * @return Curl\Query
     */
    protected function createRequest($path, array $queryParams = [], array $data = [], $timeoutMultiplier = 1)
    {
        $config = (array)\App::config()->coreV2 + [
            'url'       => null,
            'timeout'   => null,
            'client_id' => null,
        ];

        $query = $this->getCurl()->createQuery();
        $request = $query->request;
        //$request->options = get default curl options

        // заголовки
        $request->options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        // строка запроса
        $request->options[CURLOPT_URL] =
            preg_replace('/\/v2\/$/', '/', $config['url'])
            . $path
            . ((bool)$queryParams
                ? ('?' . http_build_query($queryParams + ['client_id' => $config['client_id']]))
                : ''
            )
        ;
        // POST-параметры
        if ((bool)$data) {
            $request->options[CURLOPT_POST] = true;
            $request->options[CURLOPT_POSTFIELDS] = json_encode($data);
        }
        // таймаут
        $request->options[CURLOPT_TIMEOUT_MS] = $config['timeout'] * 1000 * $timeoutMultiplier;

        return $query;
    }

    /**
     * @param Curl\Response $response
     * @return array|null
     * @throws CoreQueryException|\Exception
     */
    protected function decodeResponse(Curl\Response $response)
    {
        $result = null;

        if ($response->error) {
            throw $response->error;
        }

        $result = $this->jsonToArray($response->body);
        if (array_key_exists('error', $result)) {
            $result = array_merge(['code' => 0, 'message' => null, 'detail' => []], $result['error']);

            $error = new CoreQueryException($result['message'], $result['code']);
            $error->setDetail((array)$result['detail']);

            throw $error;
        }

        return $result;
    }
}
