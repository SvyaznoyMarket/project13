<?php

namespace Model\Product\Category;

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
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::scmsClient();

        $entity = null;
        $client->addQuery('category/get/v1',
            [
                'slug'   => $token,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                if ($data && is_array($data)) {
                    $entity = new \Model\Product\Category\Entity($data);
                }
            },
            function(\Exception $e) {
                if (404 == $e->getCode()) {
                    \App::exception()->remove($e);
                }
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param string $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     * @param string|null $brandSlug
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback, $brandSlug = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'slug' => $token,
        ];

        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        if ($brandSlug) {
            $params['brand_slug'] = $brandSlug;
        }

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview) {
            $params['load_inactive'] = 1;
        }

        \App::scmsClient()->addQuery('category/get/v1', $params, [], $callback, function(\Exception $e) {
            if (404 == $e->getCode()) {
                \App::exception()->remove($e);
            }
        });
    }

    /**
     * @param string $uid
     * @param callable $callback
     */
    public function prepareEntityByUid($uid, $callback) {
        \App::scmsClient()->addQuery('category/get/v1',
            [
                'uid'     => $uid,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            $callback,
            function(\Exception $e) {
                if (404 == $e->getCode()) {
                    \App::exception()->remove($e);
                }
            }
        );
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::scmsClient();

        $entity = null;
        $client->addQuery('category/get/v1',
            [
                'id'     => $id,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                if ($data && is_array($data)) {
                    $entity = new \Model\Product\Category\Entity($data);
                }
            },
            function(\Exception $e) {
                if (404 == $e->getCode()) {
                    \App::exception()->remove($e);
                }
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param string $uid
     * @return Entity|null
     */
    public function getEntityByUid($uid) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::scmsClient();

        $entity = null;
        $client->addQuery('category/get/v1',
            [
                'uid'     => $uid,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity) {
                if ($data && is_array($data)) {
                    $entity = new \Model\Product\Category\Entity($data);
                }
            },
            function(\Exception $e) {
                if (404 == $e->getCode()) {
                    \App::exception()->remove($e);
                }
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = \App::scmsClient();

        $collection = [];
        $client->addQuery('category/gets',
            [
                'ids'    => $ids,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$collection) {
                if (isset($data['categories']) && is_array($data['categories'])) {
                    foreach ($data['categories'] as $item) {
                        if ($item && is_array($item)) {
                            $collection[] = new \Model\Product\Category\Entity($item);
                        }
                    }
                }
            }
        );

        $client->execute();

        return $collection;
    }

    /**
     * @param array                $ids
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!$ids) {
            return;
        }

        if (count($ids) > \App::config()->search['categoriesLimit']) {
            // ограничиваем, чтобы не было 414 Request-URI Too Large // при кол-во 500 была ошибка, 475 - уже нет
            $ids = array_slice($ids, 0, \App::config()->search['categoriesLimit']);
        }

        $params = [
            'ids' => $ids,
        ];

        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        \App::scmsClient()->addQuery('category/gets', $params, [], function($data) use(&$done) {
            if (!$done) {
                return;
            }

            if (isset($data['categories'])) {
                $data = $data['categories'];
            }

            $done($data);
        }, $fail);
    }

    /**
     * @param \Model\Region\Entity $region
     * @param int                  $maxLevel
     * @param int                  $count_local
     * @param callback             $done
     * @param callback|null        $fail
     */
    public function prepareTreeCollection(\Model\Region\Entity $region = null, $maxLevel = null, $count_local = 0, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'is_load_parents' => true,
            'count_local' => $count_local
        ];
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        \App::searchClient()->addQuery('category/tree', $params, [], $done, $fail);
    }

    /**
     * @param $rootId
     * @param \Model\Region\Entity $region
     * @param int $maxLevel
     * @param callback $done
     * @param callback|null $fail
     */
    public function prepareTreeCollectionByRoot($rootId, \Model\Region\Entity $region = null, $maxLevel = null, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'is_load_parents' => true,
            'root_id'         => $rootId,
        ];
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview === true) $params = array_merge($params, ['load_inactive' => 1, 'load_empty' => 1]);

        \App::searchClient()->addQuery('category/tree', $params, [], $done, $fail);
    }

    /**
     * @param Entity               $category
     * @param \Model\Region\Entity $region
     */
    public function prepareEntityBranch($rootId, Entity $category, \Model\Region\Entity $region = null, array $filters = []) {
        $params = [
            'root_id'         => $rootId,
            'max_level'       => 5,
            'is_load_parents' => true,
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }

        if (!empty($filters)) {
            $params['filter']['filters'] = $filters;
        }

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview === true) $params = array_merge($params, ['load_inactive' => 1, 'load_empty' => 1]);

        \App::searchClient()->addQuery('category/tree', $params, [], function($data) use (&$category, &$region) {
            /**
             * Загрузка дочерних и родительских узлов категории
             *
             * @param \Model\Product\Category\Entity $category
             * @param array $data
             * @use \Model\Region\Entity $region
             */
            $loadBranch = function(\Model\Product\Category\Entity $category, array $data) use (&$region) {
                // только при загрузке дерева ядро может отдать нам количество товаров в ней
                if ($region && isset($data['product_count'])) {
                    $category->setProductCount($data['product_count']);
                }

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        if (is_array($childData)) {
                            $category->addChild(new \Model\Product\Category\Entity($childData));
                        }
                    }
                }
            };

            /**
             * Перебор дерева категорий на данном уровне
             *
             * @param $data
             * @use $iterateLevel
             * @use $loadBranch
             * @use $category     Текущая категория каталога
             */
            $iterateLevel = function($data) use(&$iterateLevel, &$loadBranch, $category) {
                if (!is_array($data)) {
                    return;
                }

                $item = reset($data);
                if (!$item || !is_array($item)) return;

                $level = (int)$item['level'];
                if ($level < $category->getLevel()) {
                    // если текущий уровень меньше уровня категории, загружаем данные для предков и прямого родителя категории
                    $ancestor = new \Model\Product\Category\Entity($item);
                    if (1 == ($category->getLevel() - $level)) {
                        $loadBranch($ancestor, $item);
                        $category->setParent($ancestor);
                    }
                    $category->addAncestor($ancestor);
                } else if ($level == $category->getLevel()) {
                    // если текущий уровень равен уровню категории, пробуем найти данные для категории
                    foreach ($data as $item) {
                        // ура, наконец-то наткнулись на текущую категорию
                        if ($item['id'] == $category->getId()) {
                            $loadBranch($category, $item);
                            return;
                        }
                    }
                }

                $item = reset($data);
                if (isset($item['children'])) {
                    $iterateLevel($item['children']);
                }
            };

            $iterateLevel($data);
        });
    }

    public function prepareEntityHasChildren(Entity $category) {
        $params = [
            'root_id' => $category->getId(),
            'is_load_parents' => false,
//            'max_level' => $category->getLevel(), // Не выбираем дочерние категории
            'max_level' => $category->getLevel() + 1, // TODO: временный хак
            'region_id' => \App::user()->getRegion()->getId(),
        ];

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview === true) {
            $params['load_inactive'] = 1;
            $params['load_empty'] = 1;
        }

        \App::searchClient()->addQuery('category/tree', $params, [], function($data) use (&$category) {
            if (is_array($data)) {
                $data = reset($data);
//                if (isset($data['has_children'])) {
//                    $category->setHasChild($data['has_children']);
//                }

                // TODO: временный хак
                if (isset($data['children'][0])) {
                    $category->setHasChild(true);
                }
            }
        });
    }
}