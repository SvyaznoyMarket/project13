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
        $client->addQuery('product/get',
            [
                'select_type' => 'slug',
                'slug'        => $token,
                'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
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
        $client->addQuery('product/get',
            [
                'select_type' => 'id',
                'id'          => $id,
                'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
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
     * @param array $tokens
     * @param \Model\Region\Entity $region
     * @return Entity[]
     */
    public function getCollectionByToken(array $tokens, \Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$tokens) return [];

        $client = clone $this->client;

        $collection = [];
        $entityClass = $this->entityClass;
        $client->addQuery('product/get', [
            'select_type' => 'slug',
            'slug'        => $tokens,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], function($data) use (&$collection, $entityClass) {
            foreach ($data as $entity) {
                $collection[] = new $entityClass($entity);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);

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

        $client = clone $this->client;

        $collection = [];
        $entityClass = $this->entityClass;

        $client->addQuery('product/get', [
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], function($data) use (&$collection, $entityClass) {
            foreach ($data as $entity) {
                $collection[] = new $entityClass($entity);
            }
        });
        
        $client->execute(\App::config()->coreV2['retryTimeout']['short'], \App::config()->coreV2['retryCount']);

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

        $chunckedIds = array_chunk($ids, 50);

        $collection = [];
        $entityClass = $this->entityClass;
        foreach ($chunckedIds as $i => $chunk) {
            $client->addQuery('product/get',
                [
                    'select_type' => 'id',
                    'id'          => $chunk,
                    'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                ],
                [],
                function($data) use(&$collection, $entityClass, $i) {
                    foreach ($data as $item) {
                        $collection[$i][] = new $entityClass($item);
                }
            });
        }

        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $result = [];
        foreach ($collection as $chunk) {
            $result = array_merge($result, $chunk);
        }

        return $result;
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
                if (!isset($collectionById[$id])) {
                    \App::logger()->error(sprintf('В списке %s отсутствует товар #%s', json_encode($collectionById), $id));
                    \App::exception()->add(new \Exception(sprintf('В списке %s отсутсвует один или несколько товаров', json_encode($collectionById))));
                    continue;
                }
                $collection[] = $collectionById[$id];
            }

            $iterators[] = new \Iterator\EntityPager($collection, $data['count']);
        }

        return $iterators;
    }


    /**
     * Фильтрует аксессуары согласно разрешенным в json категориям
     * Возвращает массив с аксессуарами, сгруппированными по категориям
     *
     * @param $product
     * @return array
     */
    public static function filterAccessoryId(&$product, $category = null, $limit = null) {

        // формируем запрос к апи получаем json с разрешенными в качестве аксессуаров категориями

        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $product->getCategory();
        if (!(bool)$categories) {
            return array();
        }

        $categoryTokens = [];
        foreach ($categories as $iCategory) {
            $categoryTokens[] = $iCategory->getToken();
        }

        $productJson = array();

        $dataStore = \App::dataStoreClient();
        $query = sprintf('catalog/%s/%s.json', implode('/', $categoryTokens), $product->getToken());
        $dataStore->addQuery($query, [], function ($data) use (&$productJson) {
            if($data) $productJson = $data;
        });
        $dataStore->execute();

        if (!$productJson) return array();

        // массив токенов категорий, разрешенных в json
        $jsonCategoryToken = $productJson['accessory_category_token'];

        // текущие аксессуары
        $productAccessoryId = $product->getAccessoryId();
        $repository = \RepositoryManager::product();
        if ((bool)$productAccessoryId) {
            try {
                $accessories = $repository->getCollectionById($productAccessoryId);
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);
                $accessories = [];
            }
        }

        // отсеиваем аксессуары, которые не относятся к разрешенным категориям
        $accessories = array_filter($accessories, function($accessory) use(&$jsonCategoryToken) {

            // массив токенов категорий к которым относится аксессуар
            $accessoryCategoryToken = array_map(function($accessoryCategory) {
                return $accessoryCategory->getToken();
            }, $accessory->getCategory());

            // есть ли общие категории между категориями аксессуара и разрешенными в json
            $commonCategories = array_intersect($jsonCategoryToken, $accessoryCategoryToken);
            
            return !empty($commonCategories);
        });

        // собираем id аксессуаров после фильтрации и устанавливаем их продукту
        $productAccessoryId = array_map(function($accessory){ return $accessory->getId(); }, $accessories);

        // ограничиваем количество аксессуаров, которое нужно показывать
        // например на вкладка Популярные аксессуары, открывающаяся при загрузке карточки товара
        // должна содержать максимум 8 аксессуаров
        if($limit) {
            $productAccessoryId = array_slice($productAccessoryId, 0, $limit);
        }

        // устанавливаем продукту id его аксессуаров
        $product->setAccessoryId($productAccessoryId);

        // группируем аксессуары по родительским категориям и возвращаем ($limit при этом не учитывается)
        $accessoriesGrouped = array();
        foreach ($accessories as $accessory) {
            $categories = $accessory->getCategory();
            $parentCategory = end($categories);

            if(isset($accessoriesGrouped[$parentCategory->getToken()])) {
                array_push($accessoriesGrouped[$parentCategory->getToken()]['accessories'], $accessory);
            } else {
                $accessoriesGrouped[$parentCategory->getToken()]['category'] = $parentCategory;
                $accessoriesGrouped[$parentCategory->getToken()]['accessories'] = array($accessory);
            }
        }

        return $accessoriesGrouped;
    }

}