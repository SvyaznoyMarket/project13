<?php

namespace EnterQuery;

trait QueryCacheTrait
{
    private $storageKey = 'enter/curl/query/cache';

    /**
     * @param $url
     * @param array $data
     * @return string
     */
    public function getQueryCacheId($url, $data = [])
    {
        $urlParts = parse_url($url) + ['scheme' => null, 'host' => null, 'path' => null, 'query' => null];
        parse_str($urlParts['query'], $urlQuery);
        ksort($urlQuery);
        //ksort($data);
        ksort($data);
        $data = array_keys($data);

        $id =
            $urlParts['scheme']
            . '://'
            . $urlParts['host']
            . $urlParts['path']
            . '?' . http_build_query($urlQuery)
            . ($data ? ('&' . json_encode($data)) : null)
        ;

        return $id;
    }

    /**
     * @param $id
     * @param $value
     */
    public function setQueryCache($id, $value)
    {
        $GLOBALS[$this->storageKey][$id] = $value;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getQueryCache($id)
    {
        return isset($GLOBALS[$this->storageKey][$id]) ? $GLOBALS[$this->storageKey][$id] : null;
    }
}