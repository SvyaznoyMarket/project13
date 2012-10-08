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

    public function getEntityByToken($token) {
        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => \App::user()->getRegion()->getId(),
        ));
        $data = reset($response);

        return new $this->entityClass($data);
    }

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
}