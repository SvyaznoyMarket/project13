<?php

namespace Model\Product;

use Model\Product\Property\Entity as Property;
use Model\Product\Property\Group\Entity as PropertyGroup;
use Model\Tag\Entity as Tag;

class Repository {

    const URL_V2 = 'product/get';
    const URL_V3 = 'product/get-v3';

    /** @var \Core\ClientInterface */
    private $client;
    /** @var string URL для product-get */
    private $productGetUrl = self::URL_V2;

    private $options = [];

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /** Использовать product/get V2 (обычный метод)
     * @return $this
     */
    public function useV2() {
        $this->productGetUrl = self::URL_V2;
        return $this;
    }

    /** Использовать product/get V3 (облегченный)
     * @return $this
     */
    public function useV3() {
        $this->productGetUrl = self::URL_V3;
        return $this;
    }

    /** Не запрашивать модели внутри товара
     * @return $this
     */
    public function withoutModels() {
        $this->options['withModels'] = 0;
        return $this;
    }

    /**
     * @param string $uid
     * @param        $successCallback
     */
    public function prepareEntityByUid($uid, $successCallback) {

        $this->client->addQuery($this->productGetUrl, [
            'select_type' => 'ui',
            'ui'        => [$uid],
            'geo_id'      => \App::user()->getRegion()->getId(),
        ], [], $successCallback);
    }

    /**
     * @param string               $token
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareEntityByToken($token, \Model\Region\Entity $region = null, $callback) {

        $this->client->addQuery($this->productGetUrl, [
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], $callback);
    }

    /**
     * @param $id
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {

        $medias = [];

        /** @var Entity $entity */
        $entity = null;
        $this->client->addQuery($this->productGetUrl,
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

        \App::scmsClient()->addQuery(
            'product/get-description/v1',
            ['ids' => [$id], 'media' => 1],
            [],
            function($data) use(&$medias) {
                if (isset($data['products']) && is_array($data['products'])) {
                    $product = reset($data['products']);
                    if (isset($product['medias'])) {
                        $medias = array_map(function($media) { return new \Model\Media($media); }, $product['medias']);
                    }
                }
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__]);
                \App::exception()->remove($e);
            }
        );

        $this->client->execute(\App::config()->coreV2['retryTimeout']['short']);

        if ($entity) {
            $entity->medias = $medias;
        }

        return $entity;
    }

    /**
     * @param array                $barcodes
     * @param \Model\Region\Entity $region
     * @param                      $done
     * @param                      $fail
     */
    public function prepareCollectionByBarcode(array $barcodes, \Model\Region\Entity $region = null, $done, $fail = null) {

        $this->client->addQuery($this->productGetUrl, [
            'select_type' => 'bar_code',
            'bar_code'    => $barcodes,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ], [], $done, $fail);
    }

    /**
     * @param array $ids
     * @param \Model\Region\Entity $region
     * @param bool $addScores
     * @return Entity[]
     */
    public function getCollectionById(array $ids, \Model\Region\Entity $region = null, $addScores = true) {

        if (!(bool)$ids) return [];

        /** @var \Model\Product\Entity[] $collection */
        $collection = [];
        $medias = [];
        foreach (array_chunk($ids, \App::config()->coreV2['chunk_size']) as $chunk) {
            $this->client->addQuery($this->productGetUrl,
                [
                    'select_type' => 'id',
                    'id'          => $chunk,
                    'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                ] + $this->options,
                [],
                function($data) use(&$collection) {
                    if (is_array($data)) {
                        foreach ($data as $item) {
                            $product = new \Model\Product\Entity($item);
                            $collection[$product->getId()] = $product;
                        }
                    }

                }
            );

            $this->prepareProductsMediasByIds($chunk, $medias);
        }

        $this->client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $this->setMediasForProducts($collection, $medias);

        $collection = array_values($collection);

        if ($addScores) {
            $collection = \RepositoryManager::review()->addScores($collection);
        }

        return $collection;
    }

    /**
     * @param array $ids
     * @param \Model\Region\Entity $region
     * @param $done
     * @param null $fail
     */
    public function prepareCollectionById(array $ids, \Model\Region\Entity $region = null, $done, $fail = null) {

        if (!(bool)$ids || !is_array($ids)) return;

        $this->client->addQuery($this->productGetUrl, [
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ] + $this->options, [], $done, $fail);
    }

    /**
     * @param array $uis
     * @param \Model\Region\Entity $region
     * @param $done
     * @param null $fail
     */
    public function prepareCollectionByUi(array $uis, \Model\Region\Entity $region = null, $done, $fail = null) {

        if (!(bool)$uis) return;

        $this->client->addQuery($this->productGetUrl, [
            'select_type' => 'ui',
            'ui'          => $uis,
            'geo_id'      => $region ? $region->getId() : \App::user()->getRegion()->getId(),
        ] + $this->options, [], $done, $fail);
    }

    public function prepareIteratorByFilter(array $filter = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null, $done, $fail = null) {

        $this->client->addQuery('listing/list',
            [
                'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                'filter' => [
                    'filters' => $filter,
                    'sort'    => $sort,
                    'offset'  => $offset,
                    'limit'   => $limit,
                ],
            ],
            [],
            $done,
            $fail
        );
    }

    /**
     * @param array $filter
     * @param array $sort
     * @param null $offset
     * @param null $limit
     * @param \Model\Region\Entity $region
     * @return array
     */
    public function getIdsByFilter(array $filter = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null) {

        $client = clone $this->client;

        $response = [];
        $client->addQuery('listing/list',
            [
                'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                'filter' => [
                    'filters' => $filter,
                    'sort'    => $sort,
                    'offset'  => $offset,
                    'limit'   => $limit,
                ],
            ],
            [],
            function($data) use(&$response) {
            $response = $data;
        });
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        return empty($response['list']) ? [] : $response['list'];
    }

    /** Обогащает продукты данными из SCMS
     * @param \Model\Product\Entity[] $products
     * @param string $properties Необходимые свойства товара через пробел: media property label brand category
     * @param callable $failCallback
     */
    public function enrichProductsFromScms($products, $properties, $failCallback = null) {
        // Формируем массив необходимых свойств
        $properties = array_fill_keys(array_intersect(explode(' ', (string)$properties), explode(' ', 'media property label brand category')), 1);

        if ($products && $properties) {
            \App::scmsClient()->addQuery(
                'product/get-description/v1',
                ['uids' => array_map(function(\Model\Product\Entity $product) { return $product->getUi(); }, $products)] + $properties,
                [],
                function($data) use($products, $properties) {
                    foreach ($products as $product) {
                        if (isset($data['products'][$product->getUi()])) {
                            $productData = $data['products'][$product->getUi()];

                            if (isset($properties['media'])) {
                                if (isset($productData['medias']) && is_array($productData['medias'])) {
                                    foreach ($productData['medias'] as $media) {
                                        if (is_array($media)) {
                                            $product->medias[] = new \Model\Media($media);
                                        }
                                    }
                                }

                                if (isset($productData['json3d']) && is_array($productData['json3d'])) {
                                    $product->json3d = $productData['json3d'];
                                }
                            }

                            if (isset($properties['property'])) {
                                if (isset($productData['properties']) && is_array($productData['properties'])) {
                                    $product->setProperty(array_map(function($data) { return new Property($data); }, $productData['properties']));
                                }

                                if (isset($productData['property_groups']) && is_array($productData['property_groups'])) {
                                    $product->setPropertyGroup(array_map(function($data) { return new PropertyGroup($data); }, $productData['property_groups']));
                                }
                            }

                            // пока так, рефакторинг скоро будет
                            if (isset($properties['label']) && isset($productData['label']['uid'])) {
                                $product->setLabel(new Label([
                                    'id'        => @$productData['label']['core_id'],
                                    'name'      => @$productData['label']['name'],
                                    'medias'    => @$productData['label']['medias'],
                                ]));
                            }
                            
                            if (!empty($productData['brand']) && @$productData['brand']['slug'] === 'tchibo-3569') {
                                $product->setBrand(new \Model\Brand\Entity([
                                    'ui'        => @$productData['brand']['uid'],
                                    'id'        => @$productData['brand']['core_id'],
                                    'token'     => @$productData['brand']['slug'],
                                    'name'      => @$productData['brand']['name'],
                                    'media_image' => 'http://content.enter.ru/wp-content/uploads/2014/05/tchibo.png', // TODO после решения FCMS-740 заменить на URL из scms и удалить условие "@$productData['brand']['slug'] === 'tchibo-3569'"
                                ]));
                            }

                            if (isset($properties['category']) && isset($productData['categories']) && is_array($productData['categories'])) {
                                foreach ($productData['categories'] as $category) {
                                    if ($category['main']) {
                                        $product->setParentCategory(new \Model\Product\Category\Entity($category));

                                        // TODO: создать метод \Model\Product\Category\Entity::getRoot, возвращающий корневую категорию, найденную через свойство \Model\Product\Category\Entity::$parent; переименовать \Model\Product\Entity::getParentCategory в getMainCategory
                                        while (isset($category['parent']) && $category['parent']) {
                                            $category = $category['parent'];
                                        }

                                        $product->setRootCategory(new \Model\Product\Category\Entity($category));
                                        break;
                                    }
                                }
                            }
                        }
                    }
                },
                $failCallback
            );
        }
    }

    public function prepareProductsMediasByIds($productIds, &$medias) {
        \App::scmsClient()->addQuery(
            'product/get-description/v1',
            ['ids' => $productIds, 'media' => 1],
            [],
            function($data) use(&$medias) {
                if (isset($data['products']) && is_array($data['products'])) {
                    foreach ($data['products'] as $product) {
                        if (isset($product['core_id']) && isset($product['medias'])) {
                            $medias[$product['core_id']] = array_map(function($media) { return new \Model\Media($media); }, $product['medias']);
                        }
                    }
                }
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__]);
                \App::exception()->remove($e);
            }
        );
    }

    public function prepareProductsMediasByUids($productUids, &$medias) {
        \App::scmsClient()->addQuery(
            'product/get-description/v1',
            ['uids' => $productUids, 'media' => 1],
            [],
            function($data) use(&$medias) {
                if (isset($data['products']) && is_array($data['products'])) {
                    foreach ($data['products'] as $product) {
                        if (isset($product['core_id']) && isset($product['medias'])) {
                            $medias[$product['core_id']] = array_map(function($media) { return new \Model\Media($media); }, $product['medias']);
                        }
                    }
                }
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__]);
                \App::exception()->remove($e);
            }
        );
    }

    /**
     * @param \Model\Product\Entity[] $products
     * @param array|null $medias
     */
    public function setMediasForProducts($products, $medias) {
        foreach ($products as $product) {
            if ($product && isset($medias[$product->getId()])) {
                $product->medias = $medias[$product->getId()];
            }
        }
    }

    /**
     * Фильтрует аксессуары согласно разрешенным в json категориям
     * Возвращает массив с аксессуарами, сгруппированными по категориям
     *
     * TODO: отрефакторить этот г*код
     *
     * @param \Model\Product\Entity $product
     * @param $accessoryItems
     * @param int|null $category
     * @param int|null $limit
     * @param array|null $catalogJson
     * @param \Model\Product\Entity[] $accessories
     * @return array
     */
    public static function filterAccessoryId(&$product, &$accessoryItems, $category = null, $limit = null, $catalogJson = null, $accessories = []) {
        // массив токенов категорий, разрешенных в json
        if(is_null($catalogJson)) {
            $jsonCategoryToken = self::getJsonCategoryToken($product);
        } elseif(empty($catalogJson)) {
            $jsonCategoryToken = null;
        } else {
            $jsonCategoryToken = isset($catalogJson['accessory_category_token']) ? $catalogJson['accessory_category_token'] : null;
        }

        if(empty($jsonCategoryToken)) {
            return [];
        }

        // если передана категория - фильтруем, иначе - нет
        // например на вкладке "популярные" (токен категории не передается)
        // надо выводить первые 8 продуктов без фильтрации
        if (!$accessories) {
            if ($category) {
                // получаем аксессуары продукта отфильтрованные согласно разрешенным в json категориям
                $accessories = self::getAccessoriesFilteredByJson($product, $jsonCategoryToken);
            } else {
                // получаем аксессуары продукта
                $accessories = self::getAccessories($product);
            }
        }

        $accessoriesClone = $accessories;

        // собираем id аксессуаров после фильтрации, чтобы установить их продукту
        $productAccessoryId = array_map(function($accessory){ return $accessory->getId(); }, $accessories);

        // ограничиваем количество аксессуаров, которое нужно показывать
        // например вкладка Популярные аксессуары, открывающаяся при загрузке карточки товара,
        // должна содержать максимум 8 первых аксессуаров
        if($limit) {
            $productAccessoryId = array_slice($productAccessoryId, 0, $limit);
            $accessoriesClone = array_slice($accessoriesClone, 0, $limit);
        }

        // чтобы в IndexAction не делать повторный запрос к ядру для получения объектов-аксессуаров
        $accessoryItems = $accessoriesClone;

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
     * @param \Model\Product\Entity $product
     * @return array
     */
    public static function getJsonCategoryToken($product) {
        // формируем запрос к апи и получаем json с разрешенными в качестве аксессуаров категориями

        $categories = $product->getCategory();
        if (!(bool)$categories) {
            return [];
        }

        $productJson = [];

        $dataStore = \App::dataStoreClient();
        $query = sprintf('catalog/%s/%s.json', implode('/', array_map(function($category){
            /** @var $category \Model\Product\Category\Entity */
            return $category->getToken();
        }, $categories)), $product->getToken());
        $dataStore->addQuery($query, [], function ($data) use (&$productJson) {
            if($data) $productJson = $data;
        });
        $dataStore->execute();

        return empty($productJson) ? $productJson : (isset($productJson['accessory_category_token']) ? $productJson['accessory_category_token'] : null);
    }


    /**
     * Получает текущие аксессуары продукта
     * Возвращает массив с продуктами-аксессуарами
     *
     * @param \Model\Product\Entity $product
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

    public function getKitProducts(\Model\Product\Entity $kitProduct, array $partProducts = [], \EnterQuery\Delivery\GetByCart $deliveryQuery = null) {
        // Получим сущности по id
        try {
            if (!$partProducts) {
                // Получим основные товары набора
                $productPartsIds = [];
                foreach ($kitProduct->getKit() as $part) {
                    $productPartsIds[] = $part->getId();
                }

                $partProducts = $this->getCollectionById($productPartsIds);
            }
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);
        }

        $result = [];

        foreach ($partProducts as $key => $product) {
            $id = $product->getId();

            $result[$id]['id'] = $id;
            $result[$id]['name'] = $product->getName();
            $result[$id]['article'] = $product->getArticle();
            $result[$id]['token'] = $product->getToken();
            $result[$id]['url'] = $product->getLink();
            $result[$id]['image'] = $product->getMainImageUrl('product_120');
            $result[$id]['product'] = $product;
            $result[$id]['price'] = $product->getPrice();
            $result[$id]['height'] = '';
            $result[$id]['width'] = '';
            $result[$id]['depth'] = '';
            $result[$id]['deliveryDate'] = '';

            // добавляем размеры
            $dimensionsTranslate = [
                'Высота' => 'height',
                'Ширина' => 'width',
                'Глубина' => 'depth'
            ];
            if ($product->getProperty()) {
                foreach ($product->getProperty() as $property) {
                    if (in_array($property->getName(), array('Высота', 'Ширина', 'Глубина'))) {
                        $result[$id][$dimensionsTranslate[$property->getName()]] = $property->getValue();
                    }
                }
            }
        }

        foreach ($result as &$value) {
            $value['count'] = 0;
        }

        foreach ($kitProduct->getKit() as $kitPart) {
            if (isset($result[$kitPart->getId()])) $result[$kitPart->getId()]['count'] = $kitPart->getCount();
        }

        $deliveryItems = [];
        foreach ($result as $item) {
            $deliveryItems[] = array(
                'id'    => $item['product']->getId(),
                'quantity' => isset($item['count']) ? $item['count'] : 1
            );
        }

        $deliveryData = (new \Controller\Product\DeliveryAction())->getResponseData($deliveryItems, \App::user()->getRegion()->getId(), $deliveryQuery);

        if ($deliveryData['success']) {
            foreach ($deliveryData['product'] as $product) {
                $id = $product['id'];
                $date = $product['delivery'][0]['date']['value'];
                $result[$id]['deliveryDate'] = $date;
            }

        }

        return $result;
    }
}