<?php

namespace Model\DeliveryType;

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
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = array(
            array(
                'id'          => 1,
                'token'       => 'standart',
                'name'        => 'курьерская доставка',
                'description' => '',
            ),
            array(
                'id'          => 2,
                'token'       => 'express',
                'name'        => 'экспресс доставка',
                'description' => '',
            ),
            array(
                'id'          => 3,
                'token'       => 'self',
                'name'        => 'самовывоз',
                'description' => '',
            ),
            array(
                'id'          => 4,
                'token'       => 'now',
                'name'        => 'покупка в магазине',
                'description' => '',
            ),
            array(
                'id'          => 5,
                'token'       => '',
                'name'        => 'Акция!',
                'description' => 'При оплате банковской картой связной банк - бесплатная доставка.',
            ),
        );

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if ($id == $entity->getId()) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @param int $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if ($token == $entity->getToken()) {
                return $entity;
            }
        }

        return null;
    }
}