<?php

namespace Model\Product\Category;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;
    /** @var string */
    private $entityClass = '\Model\Product\Category\Entity';

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
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        $entity = null;
        $client->addQuery('category/get',
            [
                'slug'   => [$token],
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity, $entityClass) {
                $data = reset($data);
                $entity = $data ? new $entityClass($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'slug' => [$token],
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        $this->client->addQuery('category/get', $params, [], $callback);
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        $entity = null;
        $client->addQuery('category/get',
            [
                'id'     => [$id],
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity, $entityClass) {
                $data = reset($data);
                $entity = $data ? new $entityClass($data) : null;
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param array $ids
     * @return Entity[]
     */
    public function getCollectionById(array $ids) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        $collection = [];
        $client->addQuery('category/get',
            [
                'id'    => $ids,
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$collection, $entityClass) {
                foreach ($data as $item) {
                    $collection[] = new $entityClass($item);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

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

        $params = [
            'id' => $ids,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('category/get', $params, [], $done, $fail);
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
        $client->addQuery('category/get', [
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
     * @return Entity[]
     */
    public function getRootCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        // TODO: добавить регион
        $collection = [];
        $client->addQuery('category/tree',
            [
                'max_level'       => 1,
                'is_load_parents' => false,
            ],
            [],
            function ($data) use (&$collection, $entityClass) {
                foreach ($data as $item) {
                    $collection[] = new $entityClass($item);
                }
            }
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareRootCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'max_level'       => 1,
            'is_load_parents' => false,
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        $this->client->addQuery('category/tree', $params, [], $callback);
    }

    /**
     * @param \Model\Region\Entity $region
     * @param int $maxLevel
     * @return Entity[]
     */
    public function getTreeCollection(\Model\Region\Entity $region = null, $maxLevel = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        $params = [
            'is_load_parents' => false,
        ];
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        $collection = [];
        $client->addQuery('category/tree', $params, [], function ($data) use (&$collection, $entityClass) {
            foreach ($data as $item) {
                $collection[] = new $entityClass($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param int                  $maxLevel
     * @param callback             $done
     * @param callback|null        $fail
     */
    public function prepareTreeCollection(\Model\Region\Entity $region = null, $maxLevel = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'is_load_parents' => true,
        ];
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region instanceof \Model\Region\Entity) {
            $params['region_id'] = $region->getId();
        }

        $this->client->addQuery('category/tree', $params, [], $done, $fail);
    }

    /**
     * @param Entity               $category
     * @param \Model\Region\Entity $region
     */
    public function prepareEntityBranch(Entity $category, \Model\Region\Entity $region = null) {
        $params = [
            'root_id'         => $category->getHasChild() ? $category->getId() : $category->getParentId(),
            'max_level'       => 5,
            'is_load_parents' => true,
        ];
        if ($region) {
            $params['region_id'] = $region->getId();
        }
        $this->client->addQuery('category/tree', $params, [], function($data) use (&$category, &$region) {
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
                if (\App::config()->product['globalListEnabled'] && isset($data['product_count_global'])) {
                    $category->setGlobalProductCount($data['product_count_global']);
                }

                // добавляем дочерние узлы
                if (isset($data['children']) && is_array($data['children'])) {
                    foreach ($data['children'] as $childData) {
                        $category->addChild(new \Model\Product\Category\Entity($childData));
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
                $item = reset($data);
                if (!(bool)$item) return;

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


    /**
     * Получает SEO-данные для категории из json
     * Возвращает массив с SEO-данными
     *
     * @param $category
     * @param $folder
     * @param $brand
     * @return array
     */
    public static function getSeoJson($category, $brand = null) {
        // формируем ветку категорий для последующего формирования запроса к json-апи
        $branch = [$category->getToken()];
        if(!$category->isRoot()) {
            $currentCategory = $category;
            while($parent = $currentCategory->getParent()) {
                array_unshift($branch, $parent->getToken());
                $currentCategory = $parent;
            }
            array_unshift($branch, $category->getRoot()->getToken());
        }

        // формируем запрос к апи и получаем json с SEO-данными
        $seoJson = [];

        $dataStore = \App::dataStoreClient();
        $query = sprintf('seo/'.($brand ? 'brand' : 'catalog').'/%s.json', implode('/', $branch).(empty($brand) ? '' : '-'.$brand->getToken()));
        $dataStore->addQuery($query, [], function ($data) use (&$seoJson) {
            if($data) $seoJson = $data;
        });
        
        // данные для шаблона
        $patterns = [
            'категория' => [$category->getName()],
            'сайт'      => null,
        ];
        if ($brand) {
            $patterns['бренд'] = [$brand->getName()];
        }

        $dataStore->addQuery('inflect/сайт.json', [], function($data) use (&$patterns) {
            if ($data) $patterns['сайт'] = $data;
        });

        $dataStore->execute();

        if(!empty($seoJson['content'])) {
            $replacer = new \Util\InflectReplacer($patterns);
            foreach ($seoJson['content'] as $key => $content) {
                if ($value = $replacer->get($seoJson['content'][$key])) {
                    $seoJson['content'][$key] = $value;
                }
            }
        }

        return empty($seoJson) ? [] : $seoJson;
    }


}