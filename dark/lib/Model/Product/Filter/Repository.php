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
        $collection = array();

        $params = array(
            'category_id' => $category->getId(),
        );
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $response = $this->client->query('listing/filter', $params);
        $exists = array();
        foreach ($response as $data) {
            $collection[] = new Entity($data);
            $exists[] = $data['filter_id'];
        }

        if ($parent = $category->getParent()) {
            $response = $this->client->query('listing/filter', array(
                'category_id' => $parent->getId(),
                'region_id'   => \App::user()->getRegion()->getId(),
            ));
            foreach ($response as $data) {
                if (in_array($data['filter_id'], $exists)) continue;
                $collection[] = new Entity($data);
            }
        }

        return $collection;
    }
}