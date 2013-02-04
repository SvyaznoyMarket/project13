<?php

namespace Model\Product;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    private $entityClass = '\Model\Product\Entity';

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $class
     */
    public function setEntityClass($class) {
        $this->entityClass = $class;
    }

    /**
     * @param $token
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityByToken($token, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('product/get', array(
                'select_type' => 'slug',
                'slug'        => $token,
                'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$entity) {
            $data = reset($data);
            $entity = $data ? new Entity($data) : null;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        return $entity;
    }

    /**
     * @param int                  $id
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityById($id, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('product/get', array(
            'select_type' => 'id',
            'id'        => [$id],
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), [], $callback);
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('product/get', array(
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), [], $callback);
    }

    /**
     * @param $id
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('product/get', array(
            'id'     => $id,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), array(), function($data) use(&$entity) {
            $data = reset($data);
            $entity = $data ? new Entity($data) : null;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        return $entity;
    }

    /**
     * @param array $tokens
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByToken(array $tokens, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$tokens) return [];

        $response = $this->client->query('product/get', array(
            'select_type' => 'slug',
            'slug'        => $tokens,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = [];
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array $barcodes
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByBarcode(array $barcodes, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$barcodes) return [];

        $response = $this->client->query('product/get', array(
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ));

        $collection = [];
        foreach ($response as $data) {
            $collection[] = new $this->entityClass($data);
        }

        return $collection;
    }

    /**
     * @param array                $barcodes
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionByBarcode(array $barcodes, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('product/get', array(
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), [], $done, $fail);
    }

    /**
     * @param array $ids
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionById(array $ids, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return [];

        $client = clone $this->client;

        $collection = [];
        $entityClass = $this->entityClass;
        $client->addQuery('product/get', [
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], function($data) use(&$collection, $entityClass) {
            foreach ($data as $item) {
                $collection[] = new $entityClass($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        return $collection;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return;

        $this->client->addQuery('product/get', array(
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), [], $done, $fail);
    }

    /**
     * @param array $filter
     * @param \Model\Region\Entity $region
     * @return int
     */
    public function countByFilter(array $filter = [], \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $count = 0;
        $client->addQuery('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => [],
                'offset'  => null,
                'limit'   => null,
            ),
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ), [], function($data) use(&$count){
            $count = !empty($data['count']) ? (int)$data['count'] : 0;
        });
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        return $count;
    }

    /**
     * @param array $filter
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return \Iterator\EntityPager
     */
    public function getIteratorByFilter(array $filter = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        //TODO: выпилить, когда будет реализована задача CORE-675
        if (isset($sort['default'])) $sort = [];

        $response = array();
        $this->client->addQuery('listing/list', array(
            'filter' => array(
                'filters' => $filter,
                'sort'    => $sort,
                'offset'  => $offset,
                'limit'   => $limit,
            ),
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$response) {
            $response = $data;
        });
        $this->client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $collection = [];
        $entityClass = $this->entityClass;
        if (!empty($response['list'])) {
            $this->prepareCollectionById($response['list'], $region, function($data) use(&$collection, $entityClass) {
                foreach ($data as $item) {
                    $collection[] = new $entityClass($item);
                }
            });
        }
        $this->client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        return new \Iterator\EntityPager($collection, (int)$response['count']);
    }

    /**
     * @param array $filters
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return \Iterator\EntityPager[]
     */
    public function getIteratorsByFilter(array $filters = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        // собираем все идентификаторы товаров, чтобы сделать один запрос в ядро
        $ids = [];
        $response = [];
        $this->client->addQuery('listing/multilist', [], [
            'filter_list' => array_map(function($filter) use ($sort, $offset, $limit) {
                //TODO: выпилить, когда будет реализована задача CORE-675
                if (isset($sort['default'])) $sort = [];

                return [
                    'filters' => $filter,
                    'sort'    => $sort,
                    'offset'  => $offset,
                    'limit'   => $limit,
                ];
            }, $filters),
            'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], function($data) use(&$ids, &$response) {
            $response = $data;

            foreach ($data as $item) {
                $ids = array_merge($ids, $item['list']);
            }
        });
        $this->client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        if (!(bool)$response) {
            return [];
        }

        // товары сгруппированные по идентификаторам
        $collectionById = [];

        $entityClass = $this->entityClass;
        $this->prepareCollectionById($ids, null, function($data) use(&$collectionById, $entityClass){
            foreach ($data as $item) {
                $collectionById[$item['id']] = new $entityClass($item);
            }
        });
        $this->client->execute(\App::config()->coreV2['retryTimeout']['medium']);


        $iterators = [];
        foreach ($response as $data) {
            $collection = [];
            foreach ($data['list'] as $id) {
                $collection[] = $collectionById[$id];
            }

            $iterators[] = new \Iterator\EntityPager($collection, $data['count']);
        }

        return $iterators;
    }
}