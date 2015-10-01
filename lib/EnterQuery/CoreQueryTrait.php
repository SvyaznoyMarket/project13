<?php

namespace EnterQuery;

trait CoreQueryTrait
{
    use JsonTrait;

    /**
     * @param string $action
     * @param string array $query
     * @return string
     */
    public function buildUrl($action, array $query = [])
    {
        $config = (array)\App::config()->coreV2 + [
            'url'       => null,
            'timeout'   => null,
            'client_id' => null,
        ];

        $query['client_id'] = $config['client_id'];

        return
            preg_replace('/\/v2\/$/', '/', $config['url'])
            . $action
            . ($query ? ('?' . http_build_query($query)) : '')
        ;
    }

    /**
     * @param string|null $response
     * @param \EnterLab\Curl\Query $curlQuery
     * @return array
     * @throws \Exception
     */
    protected function decodeResponse(&$response, \EnterLab\Curl\Query $curlQuery)
    {
        $result = $this->jsonToArray($response);

        $exception = null;
        if ($curlQuery->response->statusCode >= 300) {
            $exception = new Exception(sprintf('Invalid http code %s', $curlQuery), (int)$curlQuery->response->statusCode);
        }

        if (isset($result['error'])) {
            $error = (array)$result['error'] + ['code' => null, 'message' => null];
            $exception = new Exception($error['message'], $error['code']);
            if (isset($error['detail']) && is_array($error['detail'])) {
                $exception->setDetail($error['detail']);
            }
        }

        if ($exception) {
            $exception->setQuery(['url' => $curlQuery->request->options[CURLOPT_URL], 'data' => isset($curlQuery->request->options[CURLOPT_POSTFIELDS]) ? $curlQuery->request->options[CURLOPT_POSTFIELDS] : null]);

            throw $exception;
        }

        return $result;
    }
}