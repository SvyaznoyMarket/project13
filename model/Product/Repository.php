<?php

namespace Model\Product;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    public function prepareIteratorByFilter(array $filter = [], array $sort = [], $offset = null, $limit = null, \Model\Region\Entity $region = null, $done, $fail = null) {
        $client = \App::searchClient();

        $client->addQuery('v2/listing/list',
            [
                'region_id' => $region ? $region->getId() : \App::user()->getRegion()->getId(),
                'filter'    => [
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

        $client = \App::searchClient();

        $response = [];
        $client->addQuery('v2/listing/list',
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

    /**
     * Подготавливает запросы к ядру и scms для получения данных товаров. После выполнения запросов те товары, для
     * которых ядро или scms не вернули данных будут удалены из массива $products (ключи массива изменены не будут), а
     * остальные товары будут заполнены данными из ядра и scms.
     *
     * См. также: SITE-5975
     *
     * @param \Model\Product\Entity[] $products У всех товаров должны быть заданы id или ui или barcode (притом, если у
     *                                          первого товара задан id, то и у всех товаров должен быть задан именно
     *                                          id; аналогично для ui и barcode)
     * @param string $options Необходимые свойства товаров, через пробел: model media property label brand category
     * @throws
     *
     * TODO разбить на несколько функций: prepareProductQueriesById, prepareProductQueriesByUi, prepareProductQueriesByBarcode
     */
    public function prepareProductQueries(array &$products, $options = '', \Model\Region\Entity $region = null) {
        $options = trim($options) ? explode(' ', (string)$options) : [];
        $unavailableOptions = array_diff($options, ['model', 'media', 'property', 'label', 'brand', 'category']);
        if ($unavailableOptions) {
            throw new \Exception('Параметр $options содержит неподдерживаемые значения: "' . implode('", "', $unavailableOptions) . '"');
        }

        if (!$products) {
            return;
        }
        
        if (!$region) {
            $region = \App::user()->getRegion();
        }

        /** @var \Model\Product\Entity $firstProduct */
        $firstProduct = reset($products);
        if ($firstProduct->id) {
            $modelProductIdentifierName = 'id';
            $coreProductIdentifierName = 'id';

            $scmsProductRequestIdentifierName = 'ids';
            $scmsProductResponseIdentifierName = 'core_id';

            $scmsProductModelRequestIdentifierName = 'ids';
            $scmsProductModelResponseIdentifierName = 'id';
        } else if ($firstProduct->ui) {
            $modelProductIdentifierName = 'ui';
            $coreProductIdentifierName = 'ui';

            $scmsProductRequestIdentifierName = 'uids';
            $scmsProductResponseIdentifierName = 'uid';

            $scmsProductModelRequestIdentifierName = 'uids';
            $scmsProductModelResponseIdentifierName = 'uid';
        } else {
            $modelProductIdentifierName = 'barcode';
            $coreProductIdentifierName = 'bar_code';

            $scmsProductRequestIdentifierName = 'barcodes';
            $scmsProductResponseIdentifierName = 'barcode';

            $scmsProductModelRequestIdentifierName = 'barcodes';
            $scmsProductModelResponseIdentifierName = 'barcode';
        }

        $productIdentifiers = array_filter(array_map(function(\Model\Product\Entity $product) use(&$modelProductIdentifierName) { return $product->$modelProductIdentifierName; }, $products));

        if (!$productIdentifiers) {
            return;
        }

        foreach (array_chunk($productIdentifiers, \App::config()->coreV2['chunk_size']) as $productIdentifierChunk) {
            \App::coreClientV2()->addQuery(
                'product/get-v3',
                [
                    'geo_id' => $region->getId(),
                    'select_type' => $coreProductIdentifierName,
                    $coreProductIdentifierName => $productIdentifierChunk,
                    'withModels' => 0,
                ],
                [],
                function($response) use(&$products, &$modelProductIdentifierName, &$coreProductIdentifierName, $productIdentifierChunk) {
                    $coreProductsByIdentifier = [];
                    call_user_func(function() use(&$response, &$coreProductsByIdentifier, &$coreProductIdentifierName) {
                        if (is_array($response)) {
                            foreach ($response as &$item) {
                                $coreProductsByIdentifier[$item[$coreProductIdentifierName]] = &$item;
                            }
                        }
                    });

                    foreach ($products as $key => $product) {
                        if (isset($coreProductsByIdentifier[$product->$modelProductIdentifierName])) {
                            $product->importFromCore($coreProductsByIdentifier[$product->$modelProductIdentifierName]);
                        } else if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }
                },
                function() use(&$products, &$modelProductIdentifierName, $productIdentifierChunk) {
                    foreach ($products as $key => $product) {
                        if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }
                }
            );

            \App::scmsClient()->addQuery(
                'product/get-description/v1',
                [$scmsProductRequestIdentifierName => $productIdentifierChunk] + array_fill_keys(array_intersect($options, ['media', 'property', 'label', 'brand', 'category']), 1),
                [],
                function($response) use(&$products, &$modelProductIdentifierName, &$scmsProductResponseIdentifierName, $productIdentifierChunk) {
                    $scmsProductsByIdentifier = [];
                    call_user_func(function() use(&$response, &$scmsProductsByIdentifier, &$scmsProductResponseIdentifierName) {
                        if (isset($response['products']) && is_array($response['products'])) {
                            foreach ($response['products'] as &$item) {
                                $scmsProductsByIdentifier[$item[$scmsProductResponseIdentifierName]] = &$item;
                            }
                        }
                    });

                    foreach ($products as $key => $product) {
                        if (isset($scmsProductsByIdentifier[$product->$modelProductIdentifierName])) {
                            $product->importFromScms($scmsProductsByIdentifier[$product->$modelProductIdentifierName]);
                        } else if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }
                },
                function() use(&$products, &$modelProductIdentifierName, $productIdentifierChunk) {
                    foreach ($products as $key => $product) {
                        if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }
                }
            );

            if (\App::config()->product['getModelInListing'] && in_array('model', $options, true)) {
                \App::scmsClient()->addQuery(
                    'api/product/get-models',
                    [
                        $scmsProductModelRequestIdentifierName => $productIdentifierChunk,
                        'geo_id' => $region->getId(),
                    ],
                    [],
                    function($response) use(&$products, &$modelProductIdentifierName, &$scmsProductModelResponseIdentifierName) {
                        $scmsProductsByIdentifier = [];
                        call_user_func(function() use(&$response, &$scmsProductsByIdentifier, &$scmsProductModelResponseIdentifierName) {
                            if (isset($response['products']) && is_array($response['products'])) {
                                foreach ($response['products'] as &$item) {
                                    $scmsProductsByIdentifier[$item[$scmsProductModelResponseIdentifierName]] = &$item;
                                }
                            }
                        });

                        foreach ($products as $key => $product) {
                            if (isset($scmsProductsByIdentifier[$product->$modelProductIdentifierName])) {
                                $product->importModelFromScms($scmsProductsByIdentifier[$product->$modelProductIdentifierName]);
                            }
                        }
                    }
                );
            }
        }
    }

    /**
     * @param array|mixed $coreItems
     * @param array|mixed $scmsDescriptionItems
     * @return \Model\Product\Entity[]
     */
    public function createProducts($coreItems = [], $scmsDescriptionItems = []) {
        $scmsDescriptionItemsByUi = [];
        foreach ($scmsDescriptionItems as $scmsDescriptionItem) {
            if (isset($scmsDescriptionItem['uid'])) {
                $scmsDescriptionItemsByUi[$scmsDescriptionItem['uid']] = $scmsDescriptionItem;
            }
        }

        $products = [];
        foreach ($coreItems as $coreItem) {
            // SITE-5975
            if (isset($coreItem['ui']) && isset($scmsDescriptionItemsByUi[$coreItem['ui']])) {
                $product = new \Model\Product\Entity($coreItem);
                $product->importFromScms($scmsDescriptionItemsByUi[$coreItem['ui']]);
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * @param Entity $kitProduct
     * @param Entity[] $partProducts
     * @param bool|true $withDelivery
     * @param \EnterQuery\Delivery\GetByCart|null $deliveryQuery
     * @return array
     */
    public function getKitProducts(\Model\Product\Entity $kitProduct, array $partProducts = [], $withDelivery = true, \EnterQuery\Delivery\GetByCart $deliveryQuery = null) {
        try {
            if (!$partProducts) {
                $partProducts = [];
                foreach ($kitProduct->getKit() as $part) {
                    $partProducts[] = new \Model\Product\Entity(['id' => $part->getId()]);
                }

                $this->prepareProductQueries($partProducts, \App::config()->lite['enabled'] ? 'media property label brand category' : 'media property');
                \App::coreClientV2()->execute();
            }
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);
        }

        $kitCountById = [];
        foreach ($kitProduct->getKit() as $kitPart) {
            $kitCountById[$kitPart->getId()] = $kitPart->getCount();
        }
        
        $result = [];

        if (!$partProducts) {
            return $result;
        }

        $deliveryItems = [];
        foreach ($partProducts as $product) {
            $id = $product->id;

            $result[$id]['id'] = $id;
            $result[$id]['ui'] = $product->ui;
            $result[$id]['name'] = $product->getName();
            $result[$id]['article'] = $product->getArticle();
            $result[$id]['token'] = $product->getToken();
            $result[$id]['url'] = $product->getLink();
            $result[$id]['image'] = $product->getMainImageUrl('product_120');
            $result[$id]['price'] = $product->getPrice();
            $result[$id]['height'] = '';
            $result[$id]['width'] = '';
            $result[$id]['depth'] = '';
            $result[$id]['deliveryDate'] = '';
            $result[$id]['count'] = isset($kitCountById[$id]) ? $kitCountById[$id] : 0;

            if (\App::config()->lite['enabled']) {
                $result[$id]['stockCount'] = $product->getStockWithMaxQuantity() ? $product->getStockWithMaxQuantity()->getQuantity() : 0;
                $result[$id]['product'] = $product;
            }

            // добавляем размеры
            $dimensionsTranslate = [
                'Высота' => 'height',
                'Ширина' => 'width',
                'Глубина' => 'depth'
            ];
            
            if ($product->getProperty()) {
                foreach ($product->getProperty() as $property) {
                    if (in_array($property->getName(), array('Высота', 'Ширина', 'Глубина'))) {
                        $result[$id][$dimensionsTranslate[$property->getName()]] = $property->getOptionValue();
                    }
                }
            }
            
            $deliveryItems[] = array(
                'id' => $id,
                'quantity' => $result[$id]['count'],
            );
        }

        if ($withDelivery) {
            $deliveryData = (new \Controller\Product\DeliveryAction())->getResponseData($deliveryItems, \App::user()->getRegion()->getId(), $deliveryQuery);

            if ($deliveryData['success']) {
                foreach ($deliveryData['product'] as $product) {
                    $id = $product['id'];
                    $date = $product['delivery'][0]['date']['value'];
                    $result[$id]['deliveryDate'] = $date;
                }

            }
        }

        return $result;
    }

    /**
     * @param \Model\Product\Entity[] $products
     * @param array $excludeProductIds
     */
    public function filterRecommendedProducts(array &$products, array $excludeProductIds = []) {
        $products = array_filter($products, function(\Model\Product\Entity $product) use($excludeProductIds) {
            return (!in_array($product->id, $excludeProductIds) && $product->isAvailable() && !$product->isInShopShowroomOnly() && !$product->isInShopOnly());
        });

        $products = array_slice($products, 0, 30);
    }

    /**
     * @param \Model\Product\Entity[] $products
     */
    public function sortRecommendedProducts(&$products) {
        try {
            usort($products, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
                if ($b->getIsBuyable() != $a->getIsBuyable()) {
                    return ($b->getIsBuyable() ? 1 : -1) - ($a->getIsBuyable() ? 1 : -1); // сначала те, которые можно купить
                } else if ($b->isInShopOnly() != $a->isInShopOnly()) {
                    return ($b->isInShopOnly() ? -1 : 1) - ($a->isInShopOnly() ? -1 : 1); // потом те, которые можно зарезервировать
                } else if ($b->isInShopShowroomOnly() != $a->isInShopShowroomOnly()) {// потом те, которые есть на витрине
                    return ($b->isInShopShowroomOnly() ? -1 : 1) - ($a->isInShopShowroomOnly() ? -1 : 1);
                } else {
                    return (int)rand(-1, 1);
                }
            });
        } catch (\Exception $e) {}
    }

    public function getViewedProductIdsByHttpRequest(\Http\Request $request, $checkQuery = false, $limit = 30) {
        $viewedProductIds = '';
        if ($checkQuery) {
            $viewedProductIds = $request->request->get('viewedProductIds');
        }

        if (!$viewedProductIds) {
            $viewedProductIds = (string)$request->cookies->get('product_viewed');
        }

        return array_values(array_slice(array_unique(array_reverse(array_filter(explode(',', $viewedProductIds), function($productId) { return (int)$productId; }))), 0, $limit));
    }
}