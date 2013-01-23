<?php

namespace Model\Product\Filter;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @return array
     */
    public function getCollectionByCategory(\Model\Product\Category\Entity $category, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));
        $collection = array();

        $params = array(
            'category_id' => $category->getId(),
        );
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $response = $this->client->query('listing/filter', $params);
        foreach ($response as $data) {
            $collection[] = new Entity($data);
        }

        return $collection;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Model\Region\Entity           $region
     * @param                                $callback
     */
    public function prepareCollectionByCategory(\Model\Product\Category\Entity $category, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array(
            'category_id' => $category->getId(),
        );
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $this->client->addQuery('listing/filter', $params, array(), $callback);
    }
}