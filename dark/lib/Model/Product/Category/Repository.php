<?php

namespace Model\Product\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function getEntityByToken($token) {
        $response = $this->client->query('category/token', array(
            'token_list' => array($token),
            'geo_id'      => 14974,
        ));

        $data = (bool)$response ? reset($response) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @return Entity[]
     */
    public function getRootCollection() {
        $response = $this->client->query('category/tree', array(
            'max_level'       => 1,
            'is_load_parents' => false,
        ));

        $collection = array();
        foreach($response as $data){
            $collection[] = new Entity($data);
        }

        return $collection;
    }
}