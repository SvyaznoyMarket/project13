<?php

namespace Model\Product\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'slug'   => array($token),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'id'     => array($id),
            'geo_id' => \App::user()->getRegion()->getId(),
        ));
        $data = (bool)$data ? reset($data) : null;

        return $data ? new Entity($data) : null;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/get', array(
            'id'    => $ids,
            'geo_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @return Entity[]
     */
    public function getRootCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $data = $this->client->query('category/tree', array(
            'max_level'       => 1,
            'is_load_parents' => false,
        ));

        $collection = array();
        foreach($data as $item){
            $collection[] = new Entity($item);
        }

        return $collection;
    }
}