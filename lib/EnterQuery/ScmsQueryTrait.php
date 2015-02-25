<?php

namespace EnterQuery;

trait ScmsQueryTrait
{
    use JsonTrait;

    /**
     * @param string $action
     * @param string array $query
     * @return string
     */
    public function buildUrl($action, $query = [])
    {
        $config = (array)\App::config()->scms + [
            'url'       => null,
            'timeout'   => null,
        ];

        return
            $config['url']
            . $action
            . ($query ? ('?' . http_build_query($query)) : '')
        ;
    }

    /**
     * @param string|null $response
     * @return array
     * @throws \Exception
     */
    protected function decodeResponse(&$response)
    {
        return $this->jsonToArray($response);
    }
}