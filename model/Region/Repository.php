<?php

namespace Model\Region;

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
     * @return Entity|null
     */
    public function getDefaultEntity() {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        return $this->getEntityById(\App::config()->region['defaultId']);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('geo/get',
            [
                'id' => [$id],
            ],
            [],
            function($data) use(&$entity) {
                if (!is_array($data)) return;

                $data = reset($data);

                if (!empty($data['id'])) {
                    $entity = $data ? new Entity($data) : null;
                }
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param int $id
     * @param $callback
     */
    public function prepareEntityById($id, $callback) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get', array('id' => array($id)), [], $callback);
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('geo/get',
            [
                'slug' => [$token],
            ],
            [],
            function($data) use(&$entity) {
                $data = reset($data);
                $entity = $data ? new Entity($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        return $entity;
    }

    /**
     * @return Entity[]
     */
    public function getShopAvailableCollection() {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('geo/get-shop-available', [], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param $done
     */
    public function prepareShopAvailableCollection($done) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get-shop-available', [], [], $done);
    }

    /**
     * @return Entity[]
     */
    public function getShownInMenuCollection() {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('geo/get-menu-cities', [], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param $done
     */
    public function prepareShownInMenuCollection($done) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/get-menu-cities', [], [], $done);
    }

    /**
     * @param string        $ip
     * @param callback      $done
     * @param callback|null $fail
     */
    public function prepareEntityByIp($ip, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('geo/locate', ['ip' => $ip], [], $done, $fail);
    }
}