<?php

namespace Model\Product\Filter;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @return Entity[]
     */
    public function getCollectionByCategory($category, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));
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
}