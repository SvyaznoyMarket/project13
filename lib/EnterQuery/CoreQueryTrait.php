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
     * @return array
     * @throws \Exception
     */
    protected function decodeResponse(&$response, $statusCode)
    {
        $result = $this->jsonToArray($response);

        if ($statusCode >= 300) {
            throw new \Exception(sprintf('Invalid http code %s', $statusCode), (int)$statusCode);
        }

        if (array_key_exists('error', $result)) {
            $error = (array)$result['error'] + ['code' => null, 'message' => null];

            throw new \Exception($error['message'], $error['code']);
        }

        return $result;
    }
}