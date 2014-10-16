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

        $client->execute();

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

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview) $params['load_inactive'] = 1;

        $this->client->addQuery('category/get', $params, [], $callback);
    }

    /**
     * @param string $token
     * @return Entity|null
     */
    public function getEntityByUi($ui) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;
        $entityClass = $this->entityClass;

        $entity = null;
        $client->addQuery('category/get',
            [
                'ui'   => [$ui],
                'geo_id' => \App::user()->getRegion()->getId(),
            ],
            [],
            function ($data) use (&$entity, $entityClass) {
                $data = reset($data);
                $entity = $data ? new $entityClass($data) : null;
            }
        );

        $client->execute();

        return $entity;
    }

    /**
     * @param string               $ui
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByUi($ui, \Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [
            'ui' => [$ui],
        ];
        if ($region instanceof \Model\Region\Entity) {
            $params['geo_id'] = $region->getId();
        }

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview) $params['load_inactive'] = 1;

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

        $client->execute();

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
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        if (!(bool)$ids) return;
        if (count($ids) > \App::config()->search['categoriesLimit']) {
            // ограничиваем, чтобы не было 414 Request-URI Too Large // при кол-во 500 была ошибка, 475 - уже нет
            $ids = array_slice($ids, 0, \App::config()->search['categoriesLimit']);
        }

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

        $client->execute();

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

        $client->execute();

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

        $client->execute(\App::config()->coreV2['retryTimeout']['long'], 2);

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param int                  $maxLevel
     * @param int                  $count_local
     * @param callback             $done
     * @param callback|null        $fail
     */
    public function prepareTreeCollection(\Model\Region\Entity $region = null, $maxLevel = null, $count_local = 0, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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

        $this->client->addQuery('category/tree', $params, [], $done, $fail);
    }

    /**
     * @param $rootId
     * @param \Model\Region\Entity $region
     * @param int $maxLevel
     * @param callback $done
     * @param callback|null $fail
     */
    public function prepareTreeCollectionByRoot($rootId, \Model\Region\Entity $region = null, $maxLevel = null, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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

        // SITE-3524 Поддержка неактивных категорий для отладки страниц на preview.enter.ru
        if (\App::config()->preview === true) $params = array_merge($params, ['load_inactive' => 1, 'load_empty' => 1]);

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
     * @param $brand
     * @return array
     */
    public static function getSeoJson($category, $brand = null, $shopScriptSeo = []) {
        $dataStore = \App::dataStoreClient();
        $shopScript = \App::shopScriptClient();

        // формируем ветку категорий для последующего формирования запроса к json-апи
        $branch = [$category->getToken()];
        if (!$category->isRoot()) {
            $currentCategory = $category;
            while($parent = $currentCategory->getParent()) {
                array_unshift($branch, $parent->getToken());
                $currentCategory = $parent;
            }
        }

        // формируем запрос к апи и получаем json с SEO-данными
        $seoJson = [];

        if($brand || !\App::config()->shopScript['enabled']) {
            $query = sprintf('seo/'.($brand ? 'brand' : 'catalog').'/%s.json', implode('/', $branch).(empty($brand) ? '' : '-'.$brand->getToken()));
            $dataStore->addQuery($query, [], function ($data) use (&$seoJson) {
                if($data) $seoJson = $data;
            });
        } else {
            $seoJson = $shopScriptSeo;
        }

        // данные для шаблона
        $patterns = [
            'категория' => [$category->getName()],
            'сайт'      => null,
        ];
        if ($brand) {
            $patterns['бренд'] = [$brand->getName()];
        }

        $patterns['сайт'] = $dataStore->query('/inflect/сайт.json');

        $dataStore->execute();

        if(!empty($seoJson['content'])) {
            if (!is_array($seoJson['content'])) {
                $seoJson['content'] = [$seoJson['content']];
            }

            $replacer = new \Util\InflectReplacer($patterns);
            foreach ($seoJson['content'] as $key => $content) {
                if ($value = $replacer->get($seoJson['content'][$key])) {
                    $seoJson['content'][$key] = $value;
                }
            }
        }

        return empty($seoJson) ? [] : $seoJson;
    }

    /**
     * Получает горячие ссылки из seoCatalogJson
     *
     * @param $seoCatalogJson
     * @return array
     */
    public function getHotlinksBySeoCatalogJson($seoCatalogJson) {
        $hotlinks = empty($seoCatalogJson['hotlinks']) ? [] : $seoCatalogJson['hotlinks'];
        $autohotlinks = empty($seoCatalogJson['autohotlinks']) ? [] : $seoCatalogJson['autohotlinks'];

        // удаляем дубликаты из autohotlinks, встречающиеся в hotlinks
        // такой подход кроме прочего позволяет в hotlinks отключать показ горячей ссылки
        // даже если в autohotlinks она активна
        foreach ($hotlinks as $hotlink) {
            foreach ($autohotlinks as $autokey => $autohotlink) {
                if($autohotlink['title'] == $hotlink['title']) {
                    unset($autohotlinks[$autokey]);
                }
            }
        }

        $hotlinks = array_merge($hotlinks, $autohotlinks);

        // оставляем только активные (ссылки у которых не задан active считаем активными для
        // поддержки старого json)
        $hotlinks = array_values(array_filter($hotlinks, function($hotlink) {
            return !isset($hotlink['active']) || isset($hotlink['active']) && (bool)$hotlink['active'];
        }));

        return $hotlinks;
    }

    /**
     * Получает catalog json для данной категории
     * Возвращает массив с токенами категорий
     *
     * @param $category
     * @return array
     */
    public function getCatalogJson($category) {
        if(empty($category) || !is_object($category)) return [];

        /** @var \Model\Product\Category\Entity $category */

        // формируем ветку категорий для последующего формирования запроса к json-апи
        /*
        $branch = [$category->getToken()];
        if(!$category->isRoot()) {
            $currentCategory = $category;
            while($currentCategory = $currentCategory->getParent()) {
                array_unshift($branch, $currentCategory->getToken());
            }
            $root = $category->getRoot();
            if($root && !in_array($root->getToken(), $branch)) {
                array_unshift($branch, $root->getToken());
            }
        }
        */

        $thisRepository = $this;
        $catalogJson = [];
        \App::scmsClient()->addQuery(
            'category/get',
            ['uid' => $category->getUi(), 'geo_id' => \App::user()->getRegion()->getId()],
            [],
            function ($data) use (&$catalogJson, $thisRepository) {
                $catalogJson = $thisRepository->convertScmsDataToOldCmsData($data);
            },
            function(\Exception $e) {
                \App::exception()->add($e);
            }
        );
        \App::scmsClient()->execute();

        // AB-test по сортировкам SITE-1991
        $abTestJson = \App::abTestJson($catalogJson);
        if ($abTestJson && $abTestJson->getCase()->getKey() != 'default') {
            return $abTestJson->getTestCatalogJson();
        }

        return $catalogJson;
    }

    public function convertScmsDataToOldCmsData($data) {
        $result = [];

        if (is_array($data)) {
            if (isset($data['uid'])) {
                $result['ui'] = $data['uid'];
            }

            if (isset($data['properties']['bannerPlaceholder'])) {
                $result['bannerPlaceholder'] = $data['properties']['bannerPlaceholder'];
            }

            if (isset($data['properties']['smartchoice']['enabled'])) {
                $result['smartchoice'] = $data['properties']['smartchoice']['enabled'];
            }

            if (isset($data['properties']['appearance']['category_class'])) {
                $result['category_class'] = $data['properties']['appearance']['category_class'];
            }

            if (isset($data['properties']['appearance']['promo_token'])) {
                $result['promo_token'] = $data['properties']['appearance']['promo_token'];
            }

            if (isset($data['properties']['appearance']['use_logo'])) {
                $result['use_logo'] = $data['properties']['appearance']['use_logo'];
            }

            if (isset($data['properties']['appearance']['logo_path'])) {
                $result['logo_path'] = $data['properties']['appearance']['logo_path'];
            }

            if (isset($data['properties']['appearance']['use_lens'])) {
                $result['use_lens'] = $data['properties']['appearance']['use_lens'];
            }

            if (isset($data['properties']['appearance']['is_new'])) {
                $result['is_new'] = (bool)$data['properties']['appearance']['is_new'];
            }

            if (isset($data['properties']['appearance']['default']['listing_style'])) {
                $result['listing_style'] = $data['properties']['appearance']['default']['listing_style'];
            }

            if (isset($data['properties']['appearance']['default']['promo_style'])) {
                $result['promo_style'] = $data['properties']['appearance']['default']['promo_style'];
            }

            if (isset($data['properties']['appearance']['pandora']['sub_category_filters_exclude']) && is_array($data['properties']['appearance']['pandora']['sub_category_filters_exclude'])) {
                $result['sub_category_filters_exclude'] = [];
                foreach ($data['properties']['appearance']['pandora']['sub_category_filters_exclude'] as $item) {
                    if (isset($item['filter_token'])) {
                        $result['sub_category_filters_exclude'][] = $item['filter_token'];
                    }
                }
            }

            if (isset($data['properties']['appearance']['pandora']['sub_category_filter_menu'])) {
                $result['sub_category_filter_menu'] = $data['properties']['appearance']['pandora']['sub_category_filter_menu'];
            }

            if (isset($data['properties']['appearance']['tchibo']['root_id'])) {
                $result['root_category_menu']['root_id'] = $data['properties']['appearance']['tchibo']['root_id'];
            }

            if (isset($data['properties']['appearance']['tchibo']['image'])) {
                $result['root_category_menu']['image'] = $data['properties']['appearance']['tchibo']['image'];
            }

            if (isset($data['properties']['appearance']['tchibo']['red_category_id'])) {
                $result['tchibo_menu']['style']['name'] = [$data['properties']['appearance']['tchibo']['red_category_id'] => 'color:red;'];
            }

            if (isset($data['properties']['appearance']['show_branch_menu'])) {
                $result['show_branch_menu'] = $data['properties']['appearance']['show_branch_menu'];
            }

            if (isset($data['properties']['appearance']['show_side_panels'])) {
                $result['show_side_panels'] = $data['properties']['appearance']['show_side_panels'];
            }

            if (isset($data['properties']['sort']['json'])) {
                $result['sort'] = $data['properties']['sort']['json'];
            }

            if (isset($data['properties']['related_categories']['related_categories'])) {
                $result['related_categories'] = $data['properties']['related_categories']['related_categories'];
            }

            if (isset($data['properties']['search_hints']['search_hints']) && is_array($data['properties']['search_hints']['search_hints'])) {
                $result['search_hints'] = [];
                foreach ($data['properties']['search_hints']['search_hints'] as $val) {
                    if (isset($val['search_string'])) {
                        $result['search_hints'][] = $val['search_string'];
                    }
                }
            }

            if (isset($data['properties']['trust_factors']['top'])) {
                $result['trustfactor_top'] = $data['properties']['trust_factors']['top'];
            }

            if (isset($data['properties']['trust_factors']['main'])) {
                $result['trustfactor_main'] = $data['properties']['trust_factors']['main'];
            }

            if (isset($data['properties']['trust_factors']['right']) && is_array($data['properties']['trust_factors']['right'])) {
                $result['trustfactor_right'] = [];
                foreach ($data['properties']['trust_factors']['right'] as $val) {
                    if (isset($val['type'])) {
                        $result['trustfactor_right'][] = $val['type'];
                    }
                }
            }

            if (isset($data['properties']['trust_factors']['content']) && is_array($data['properties']['trust_factors']['content'])) {
                $result['trustfactor_content'] = [];
                foreach ($data['properties']['trust_factors']['content'] as $val) {
                    if (isset($val['type'])) {
                        $result['trustfactor_content'][] = $val['type'];
                    }
                }
            }

            if (isset($data['properties']['trust_factors']['exclude_token']) && is_array($data['properties']['trust_factors']['exclude_token'])) {
                $result['trustfactor_exclude_token'] = [];
                foreach ($data['properties']['trust_factors']['exclude_token'] as $val) {
                    if (isset($val['type'])) {
                        $result['trustfactor_exclude_token'][] = $val['type'];
                    }
                }
            }

            if (isset($data['properties']['promo_slider'])) {
                $result['promo_slider'] = $data['properties']['promo_slider'];
            }

            if (isset($data['properties']['products']['accessory_category_token'])) {
                $result['accessory_category_token'] = $data['properties']['products']['accessory_category_token'];
            }
        }

        return $result;
    }
}