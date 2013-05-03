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
     * @param array $filter
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return array
     */
    public function getCollectionByFilter(array $filter = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null) {
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

        return $collection;
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
        // массив токенов категорий, разрешенных в json
        $jsonCategoryToken = self::getJsonCategoryToken($product);

        if(empty($jsonCategoryToken)) {
            return [];
        }

        // если передана категория - фильтруем, иначе - нет
        // например на вкладке "популярные" (токен категории не передается)
        // надо выводить первые 8 продуктов без фильтрации
        if($category) {
            // получаем аксессуары продукта отфильтрованные согласно разрешенным в json категориям
            $accessories = self::getAccessoriesFilteredByJson($product, $jsonCategoryToken);
        } else {
            // получаем аксессуары продукта
            $accessories = self::getAccessories($product);
        }

        // собираем id аксессуаров после фильтрации, чтобы установить их продукту
        $productAccessoryId = array_map(function($accessory){ return $accessory->getId(); }, $accessories);

        // ограничиваем количество аксессуаров, которое нужно показывать
        // например вкладка Популярные аксессуары, открывающаяся при загрузке карточки товара,
        // должна содержать максимум 8 первых аксессуаров
        if($limit) {
            $productAccessoryId = array_slice($productAccessoryId, 0, $limit);
        }

        // устанавливаем продукту id его аксессуаров
        $product->setAccessoryId($productAccessoryId);

        // группируем аксессуары по родительским категориям и возвращаем ($limit при этом не учитывается)
        // используется для построения списка категорий аксессуаров - должно быть отфильтрованным
        if(!$category) $accessories = self::filterAccessoriesByJson($accessories, $jsonCategoryToken); 
        return self::groupByCategory($accessories, 'accessories');
    }


    /**
     * Получает разрешенные в json для аксессуаров категории
     * Возвращает массив с токенами категорий
     *
     * @param $product
     * @return array
     */
    public static function getJsonCategoryToken($product) {
        // формируем запрос к апи и получаем json с разрешенными в качестве аксессуаров категориями

        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $product->getCategory();
        if (!(bool)$categories) {
            return [];
        }

        $productJson = [];

        $dataStore = \App::dataStoreClient();
        $query = sprintf('catalog/%s/%s.json', implode('/', array_map(function($category){return $category->getToken();}, $categories)), $product->getToken());
        $dataStore->addQuery($query, [], function ($data) use (&$productJson) {
            if($data) $productJson = $data;
        });
        $dataStore->execute();

        return empty($productJson) ? $productJson : $productJson['accessory_category_token'];
    }


    /**
     * Получает текущие аксессуары продукта
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param $product
     * @return array
     */
    public static function getAccessories($product) {
        // id текущих аксессуаров
        $productAccessoryId = $product->getAccessoryId();
        $accessories = [];
        if ((bool)$productAccessoryId) {
            try {
                $accessories = \RepositoryManager::product()->getCollectionById($productAccessoryId);
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);
            }
        }
        return $accessories;
    }


    /**
     * Получает аксессуары продукта отфильтрованные согласно разрешенным в json категориям
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param $product
     * @param $jsonCategoryToken
     * @return array
     */
    public static function getAccessoriesFilteredByJson($product, $jsonCategoryToken) {
        // отсеиваем среди текущих аксессуаров те аксессуары, которые не относятся к разрешенным категориям
        return array_filter(self::getAccessories($product), function($accessory) use(&$jsonCategoryToken) {

            // массив токенов категорий к которым относится аксессуар
            $accessoryCategoryToken = array_map(function($accessoryCategory) {
                return $accessoryCategory->getToken();
            }, $accessory->getCategory());

            // есть ли общие категории между категориями аксессуара и разрешенными в json
            $commonCategories = array_intersect($jsonCategoryToken, $accessoryCategoryToken);
            
            return !empty($commonCategories);
        });
    }


    /**
     * Фильтрует переданные аксессуары продукта согласно разрешенным в json категориям
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param $product
     * @param $jsonCategoryToken
     * @return array
     */
    public static function filterAccessoriesByJson($accessories, $jsonCategoryToken) {
        // отсеиваем среди текущих аксессуаров те аксессуары, которые не относятся к разрешенным категориям
        return array_filter($accessories, function($accessory) use(&$jsonCategoryToken) {

            // массив токенов категорий к которым относится аксессуар
            $accessoryCategoryToken = array_map(function($accessoryCategory) {
                return $accessoryCategory->getToken();
            }, $accessory->getCategory());

            // есть ли общие категории между категориями аксессуара и разрешенными в json
            $commonCategories = array_intersect($jsonCategoryToken, $accessoryCategoryToken);
            
            return !empty($commonCategories);
        });
    }


    /**
     * Получает аксессуары продукта из категорий, не разрешенных в json
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param $product
     * @param $jsonCategoryToken
     * @return array
     */
    public static function getAccessoriesNotInJson($product, $jsonCategoryToken) {
        // отсеиваем среди текущих аксессуаров те аксессуары, которые относятся к разрешенным категориям
        return array_filter(self::getAccessories($product), function($accessory) use(&$jsonCategoryToken) {

            // массив токенов категорий к которым относится аксессуар
            $accessoryCategoryToken = array_map(function($accessoryCategory) {
                return $accessoryCategory->getToken();
            }, $accessory->getCategory());

            // есть ли общие категории между категориями аксессуара и разрешенными в json
            $commonCategories = array_intersect($jsonCategoryToken, $accessoryCategoryToken);
            
            return empty($commonCategories);
        });
    }


    /**
     * Фильтрует переданные аксессуары продукта, оставляя не разрешенные в json
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param $product
     * @param $jsonCategoryToken
     * @return array
     */
    public static function filterAccessoriesNotInJson($accessories, $jsonCategoryToken) {
        // отсеиваем среди текущих аксессуаров те аксессуары, которые не относятся к разрешенным категориям
        return array_filter($accessories, function($accessory) use(&$jsonCategoryToken) {

            // массив токенов категорий к которым относится аксессуар
            $accessoryCategoryToken = array_map(function($accessoryCategory) {
                return $accessoryCategory->getToken();
            }, $accessory->getCategory());

            // есть ли общие категории между категориями аксессуара и разрешенными в json
            $commonCategories = array_intersect($jsonCategoryToken, $accessoryCategoryToken);
            
            return empty($commonCategories);
        });
    }


    /**
     * Группирует продукты по их родительским категориям
     * Возвращает массив с токенами категорий в качестве ключей и в качестве значений имеющий
     * массив с категорией и продуктами
     *
     * @param $products
     * @param $type
     * @return array
     */
    public static function groupByCategory($products, $type) {
        $productsGrouped = [];
        foreach ($products as $product) {
            $categories = $product->getCategory();
            $parentCategory = end($categories);
            if (!$parentCategory) continue;

            if(isset($productsGrouped[$parentCategory->getToken()])) {
                array_push($productsGrouped[$parentCategory->getToken()][$type], $product);
            } else {
                $productsGrouped[$parentCategory->getToken()] = [];
                $productsGrouped[$parentCategory->getToken()]['category'] = $parentCategory;
                $productsGrouped[$parentCategory->getToken()][$type] = [$product];
            }
        }
        return $productsGrouped;
    }


    /**
     * Создает отчеты для Бизнес-юнитов и для SEO-подрядчиков о состоянии аксессуаров
     *
     */
    public static function generateReports() {
        ini_set("auto_detect_line_endings", true);
        $source_csv_dir = \App::config()->appDir . '/report/source';
        $client = \App::coreClientV2();

        foreach (scandir($source_csv_dir) as $file) {
            if(preg_match('/^(.+)\.csv$/', $file, $matches)) {

                $dateStart = new \DateTime();

                $rootCategory = $matches[1];
                $reportBuFilepath = \App::config()->appDir . '/report/' . $dateStart->format('YmdHis') . '_' . $rootCategory . '_accessories_bu.csv';
                $reportSeoFilepath = \App::config()->appDir . '/report/' . $dateStart->format('YmdHis') . '_' . $rootCategory . '_accessories_seo.csv';
                $sourceCsvFilepath = \App::config()->appDir . '/report/source/' . $file;
                $benchmarkFilepath = \App::config()->appDir . '/report/source/benchmark.txt';
                $reportBu = fopen($reportBuFilepath, 'a');
                $reportSeo = fopen($reportSeoFilepath, 'a');

                //шапки выходных csv
                fwrite($reportBu, "Тип проблемы;URL категории товара;id,bar_code;Название товара;URL категории аксессуара;accessories bar_code;Название товара-аксессуара;Значение флага is_buyable\n");
                fwrite($reportSeo, "URL родительской категории;Кол-во товаров в родительской категории;URL категории аксессуара;Кол-во товаров в родительской категории к которым привязан хотя бы один аксессуар из категории аксессуара\n");

                // парсим csv с входными данными по продуктам
                $productsCsv = [];
                if (($source_csv = fopen($sourceCsvFilepath, "r")) !== FALSE) {
                    while (($data = fgetcsv($source_csv, 0, ",")) !== FALSE) {
                        if(!empty($data[0])) {
                            $productsCsv[$data[0]] = [
                                // 'article' => empty($data[1]) ? '' : $data[1],
                                'barcode' => empty($data[2]) ? '' : $data[2],
                                // 'url' => empty($data[3]) ? '' : $data[3],
                            ];
                        }
                    }
                    fclose($source_csv);
                }

                // аккумулятор для SEO-данных
                $categoryProductsData = [];

                // проводим анализ по частям для снижения нагрузки
                $step = 100;
                $part = 1;
                $max_parts = 1;

$timeGetAccessories = 0;
$timeGetJson = 0;
$timeExistingCategories = 0;

                while ($part <= (int)ceil(count($productsCsv) / $step)) {
                    if($part > $max_parts) break;

                    // получение массива товаров и определение не найденных товаров
                    $productIdsPart = array_slice(array_keys($productsCsv), ($part - 1) * $step, $step);
                    $products = \RepositoryManager::product()->getCollectionById($productIdsPart);
                    $productIdsPartApi = array_map(function($product){ return $product->getId();}, $products);
                    $notFoundProductIds = array_diff($productIdsPart, $productIdsPartApi);
                    foreach ($notFoundProductIds as $notFoundProductId) {
                        fwrite($reportBu, "Не найден товар;;" . $notFoundProductId . ";" . $productsCsv[$notFoundProductId]['barcode'] . ";;;;;\n");
                    }

                    foreach ($products as $product) {
                        $isBuyable = $product->getIsBuyable() ? 'true' : 'false';

                        // текущие аксессуары товара
$date1 = new \DateTime();
                        $accessories = self::getAccessories($product);
$date2 = new \DateTime();
$timeGetAccessories += $date2->getTimestamp() - $date1->getTimestamp();

                        // массив токенов категорий, разрешенных в json
$date1 = new \DateTime();
                        $jsonCategoryToken = self::getJsonCategoryToken($product);
$date2 = new \DateTime();
$timeGetJson += $date2->getTimestamp() - $date1->getTimestamp();

                        // если в json не заданы категории аксессуаров
                        if(empty($jsonCategoryToken)) {
                            fwrite($reportBu, "Не установлены категории аксессуаров в json;;".$product->getId().";".$product->getBarcode().";".$product->getName().";;;;".$isBuyable."\n");
                        }
 
                        // получаем родительскую категорию товара
                        $productCategories = $product->getCategory();
                        $productParentCategory = end($productCategories);

                        // если родительская категория не задана
                        if(empty($productParentCategory)) {
                            fwrite($reportBu, "Не установлена категория продукта;;".$product->getId().";".$product->getBarcode().";".$product->getName().";;;;".$isBuyable."\n");
                        }

                        // фильтруем аксессуары товара, оставляя только разрешенные в json
                        $accessoriesGrouped = self::groupByCategory(self::filterAccessoriesByJson($accessories, $jsonCategoryToken), 'accessories');
                        // получаем токены разрешенных в json категорий, для которых привязан хотя бы 1 аксессуар
                        $accessoryCategoryTokens = array_keys($accessoriesGrouped);
                        // получаем объекты категорий на основе указанных в json токенов
$date1 = new \DateTime();
                        $existingCategories = \RepositoryManager::productCategory()->getCollectionByToken($jsonCategoryToken);
$date2 = new \DateTime();
$timeExistingCategories += $date2->getTimestamp() - $date1->getTimestamp();
                        foreach ($jsonCategoryToken as $categoryToken) {
                            // проверяем существуют ли категории, указанные в json
                            if(!in_array($categoryToken, array_map(function($category){return $category->getToken();}, $existingCategories))) {
                                fwrite($reportBu, "Категории не существует (json);;;;;http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken.";;;".$isBuyable."\n");
                            }

                            // проверяем есть ли привязанные к товару аксессуары из данной категории json
                            if(!in_array($categoryToken, $accessoryCategoryTokens)) {
                                fwrite($reportBu, "Не привязан аксессуар из категории в json;http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '').";".$product->getId().";".$product->getBarcode().";".$product->getName().";http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken.";;;".$isBuyable."\n");
                            }

                            // проверяем количество привязанных к товару аксессуаров из данной категории json
                            if(in_array($categoryToken, $accessoryCategoryTokens) && count($accessoriesGrouped[$categoryToken]['accessories']) < 4) {
                                fwrite($reportBu, "В категории меньше четырех аксессуаров (json);http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '').";".$product->getId().";".$product->getBarcode().";".$product->getName().";http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken.";;;".$isBuyable."\n");
                            }
                        }

                        // проверяем есть ли у продукта аксессуары из категорий, не указанных в json
                        $accessoriesNotInJson = self::filterAccessoriesNotInJson($accessories, $jsonCategoryToken);
                        foreach ($accessoriesNotInJson as $accessoryNotInJson) {
                            $accessoryCategories = $accessoryNotInJson->getCategory();
                            $accessoryParentCategory = end($accessoryCategories);

                            fwrite($reportBu, "Привязан неверный аксессуар;http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '').";".$product->getId().";".$product->getBarcode().";".$product->getName().";http://www.enter.ru/catalog/".$rootCategory."/".($accessoryParentCategory ? $accessoryParentCategory->getToken() : '').";".$accessoryNotInJson->getBarcode().";".$accessoryNotInJson->getName().";".$isBuyable."\n");
                        }

                        // если у продукта не установлена родительская категория, то в формировании SEO-отчета
                        // этот продукт не участвует
                        if(!$productParentCategory) continue;

                        // получаем количество товаров в родительской категории товара
                        if(!isset($categoryProductsData[$productParentCategory->getToken()])) {
                            $categoryProductsData[$productParentCategory->getToken()]['count'] = 1;
                        } else {
                            $categoryProductsData[$productParentCategory->getToken()]['count']++;
                        }

                        // получаем количество товаров в родительской категории товара,
                        // к которым привязан хоть один аксессуар из категории в json
                        foreach ($jsonCategoryToken as $categoryToken) {
                            if(in_array($categoryToken, array_keys($accessoriesGrouped))) {
                                if(!isset($categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken])) {
                                    $categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken]['count'] = 1;
                                } else {
                                    $categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken]['count']++;
                                }
                            }
                        }
                    }

                    $part++;
                }

                // формируем отчет для SEO
                foreach ($categoryProductsData as $parentCategoryToken => $parentCategoryData) {
                    foreach ($parentCategoryData['jsonCategories'] as $accessoryCategoryToken => $accessoryCategoryData) {
                        fwrite($reportSeo, "http://www.enter.ru/catalog/".$rootCategory."/".$parentCategoryToken.";".$parentCategoryData['count'].";http://www.enter.ru/catalog/".$rootCategory."/".$accessoryCategoryToken.";".$accessoryCategoryData['count']."\n");
                    }
                }

                $dateEnd = new \DateTime();

$benchmark = fopen($benchmarkFilepath, 'a');
fwrite($benchmark, '['.date('Y-m-d H:i:s').']:'."\n");
fwrite($benchmark, 'timeGetAccessories: '.$timeGetAccessories.' сек.'."\n");
fwrite($benchmark, 'timeGetJson: '.$timeGetJson.' сек.'."\n");
fwrite($benchmark, 'timeExistingCategories: '.$timeExistingCategories.' сек.'."\n");
fwrite($benchmark, 'Общее время генерации отчетов : '. ($dateEnd->getTimestamp() - $dateStart->getTimestamp()) .' сек.'."\n");
fwrite($benchmark, "\n");
fclose($benchmark);

                fclose($reportBu);
                fclose($reportSeo);
            }
        }
    }


}