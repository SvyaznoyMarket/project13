<?php

namespace EnterQuery;

trait EventQueryTrait
{
    use JsonTrait;

    /**
     * @param string $action
     * @param string array $query
     * @return string
     */
    public function buildUrl($action, $query = [])
    {
        $config = (array)\App::config()->eventService + [
            'url'       => null,
            'client_id' => null,
            'timeout'   => null,
        ];

        //$query['client_id'] = $config['client_id'];

        return
            $config['url']
            . $action
            . ($query ? ('?' . http_build_query($query)) : '')
        ;
    }

    /**
     * @param string|null $response
     * @param $statusCode
     * @return array
     * @throws \Exception
     */
    protected function decodeResponse(&$response, $statusCode)
    {
        $result = $this->jsonToArray($response);
        if ($statusCode >= 300) {
            throw new \Exception(sprintf('Invalid http code %s', $statusCode), (int)$statusCode);
        }

        return $result;
    }
}