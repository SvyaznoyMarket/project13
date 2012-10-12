<?php

namespace Model\Product;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    private $entityClass = '\Model\Product\Entity';

    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function setEntityClass($class) {
        $this->entityClass = $class;
    }

    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return $data ? new Entity($data) : null;
    }

    /**
     * @param array $tokens
     * @return Entity[]
     */
    public function getCollectionByToken(array $tokens) {
        if (!(bool)$tokens) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $tokens,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        if (!(bool)$ids) return array();

        $response = $this->client->query('product/get', array(
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));

        $collection = array();
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array $filters
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @return \Iterator\EntityPager
     */
    public function getIteratorByFilter(array $filters = array(), array $sort = array(), $offset = null, $limit = null) {
        $response = $this->client->query('listing/list', array(
            'filter' => array(
                'filters' => $filters,
                'sort'    => $sort,
                'offset'  => $offset,
                'limit'   => $limit,
            ),
            'region_id' => \App::user()->getRegion()->getId(),
        ));

        $collection = !empty($response['list']) ? $this->getCollectionById($response['list']) : array();

        return new \Iterator\EntityPager($collection, (int)$response['count']);
    }
}