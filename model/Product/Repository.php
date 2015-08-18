<?php

namespace Model\Product;

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
     * @param $id
     * @param \Model\Region\Entity $region
     * @return Entity|null
     */
    public function getEntityById($id, \Model\Region\Entity $region = null) {
        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['id' => $id])];
        \RepositoryManager::product()->prepareProductQueries($products, 'media', $region);
        \App::coreClientV2()->execute();
        
        if ($products) {
            return $products[0];
        }
        
        return null;
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
     * которых ядро или scms не вернули данных будут удалены, а остальные товары будут заполнены данными из ядра и scms.
     * @param \Model\Product\Entity[] $products
     * @param string $scmsOptions Необходимые свойства товара через пробел: media property label brand category
     * @param \Closure $finalCallback Вызывается один раз после выполнения последнего из запросов
     * @throws
     */
    public function prepareProductQueries(array &$products, $scmsOptions = '', \Model\Region\Entity $region = null, $finalCallback = null) {
        $scmsOptions = trim($scmsOptions) ? explode(' ', (string)$scmsOptions) : [];
        $unavailableScmsOptions = array_diff($scmsOptions, ['media', 'property', 'label', 'brand', 'category']);
        if ($unavailableScmsOptions) {
            throw new \Exception('Параметр $returnDataOptions содержит неподдерживаемые значения: "' . implode('", "', $unavailableScmsOptions) . '"');
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
        } else if ($firstProduct->ui) {
            $modelProductIdentifierName = 'ui';
            $coreProductIdentifierName = 'ui';
            $scmsProductRequestIdentifierName = 'uids';
            $scmsProductResponseIdentifierName = 'uid';
        } else {
            $modelProductIdentifierName = 'barcode';
            $coreProductIdentifierName = 'bar_code';
            $scmsProductRequestIdentifierName = 'barcodes';
            $scmsProductResponseIdentifierName = 'barcode';
        }

        $productIdentifiers = array_filter(array_map(function(\Model\Product\Entity $product) use(&$modelProductIdentifierName) { return $product->$modelProductIdentifierName; }, $products));

        if (!$productIdentifiers) {
            return;
        }

        $productIdentifierChunks = array_chunk($productIdentifiers, \App::config()->coreV2['chunk_size']);
        $callCount = 0;
        $expectedCallCount = count($productIdentifierChunks) * 2;
        $firstException = null;

        foreach ($productIdentifierChunks as $productIdentifierChunk) {
            \App::coreClientV2()->addQuery(
                $this->productGetUrl,
                [
                    'geo_id' => $region->getId(),
                    'select_type' => $coreProductIdentifierName,
                    $coreProductIdentifierName => $productIdentifierChunk,
                ] + $this->options,
                [],
                function($response) use(&$products, &$modelProductIdentifierName, &$coreProductIdentifierName, &$productIdentifierChunk, &$callCount, &$expectedCallCount, &$firstException, &$finalCallback) {
                    $callCount++;

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

                    if ($finalCallback && $callCount == $expectedCallCount) {
                        $finalCallback($firstException);
                    }
                },
                function(\Exception $e) use(&$products, &$modelProductIdentifierName, &$productIdentifierChunk, &$callCount, &$expectedCallCount, &$firstException, &$finalCallback) {
                    $callCount++;
                    
                    if (!$firstException) {
                        $firstException = $e;
                    }

                    foreach ($products as $key => $product) {
                        if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }

                    if ($finalCallback && $callCount == $expectedCallCount) {
                        $finalCallback($firstException);
                    }
                }
            );
        }

        // TODO: удалить данный блок после реализации FCMS-778
        call_user_func(function() use(&$products, &$productIdentifiers, &$modelProductIdentifierName, &$scmsProductRequestIdentifierName, &$scmsProductResponseIdentifierName) {
            if ($modelProductIdentifierName === 'barcode') {
                \App::coreClientV2()->execute();

                $modelProductIdentifierName = 'id';
                $scmsProductRequestIdentifierName = 'ids';
                $scmsProductResponseIdentifierName = 'core_id';

                $productIdentifiers = array_filter(array_map(function(\Model\Product\Entity $product) use(&$entityIdentifierName) { return $product->$entityIdentifierName; }, $products));
            }
        });

        // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
        foreach ($productIdentifierChunks as $productIdentifierChunk) {
            \App::scmsClient()->addQuery(
                'product/get-description/v1',
                [$scmsProductRequestIdentifierName => $productIdentifierChunk] + array_fill_keys($scmsOptions, 1),
                [],
                function($response) use(&$products, &$modelProductIdentifierName, &$scmsProductResponseIdentifierName, &$productIdentifierChunk, &$callCount, &$expectedCallCount, &$firstException, &$finalCallback) {
                    $callCount++;

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

                    if ($finalCallback && $callCount == $expectedCallCount) {
                        $finalCallback($firstException);
                    }
                },
                function(\Exception $e) use(&$products, &$modelProductIdentifierName, &$productIdentifierChunk, &$callCount, &$expectedCallCount, &$firstException, &$finalCallback) {
                    $callCount++;
                    
                    if (!$firstException) {
                        $firstException = $e;
                    }

                    foreach ($products as $key => $product) {
                        if (in_array($product->$modelProductIdentifierName, $productIdentifierChunk, true)) {
                            unset($products[$key]);
                        }
                    }

                    if ($finalCallback && $callCount == $expectedCallCount) {
                        $finalCallback($firstException);
                    }
                }
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
            }
        );
    }

    /**
     * @param Entity[] $partProducts
     * @return array
     */
    public function getKitProducts(\Model\Product\Entity $kitProduct, array $partProducts = [], \EnterQuery\Delivery\GetByCart $deliveryQuery = null) {
        try {
            if (!$partProducts) {
                $partProducts = [];
                foreach ($kitProduct->getKit() as $part) {
                    $partProducts[] = new \Model\Product\Entity(['id' => $part->getId()]);
                }

                $this->prepareProductQueries($partProducts, 'media');
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
            $result[$id]['product'] = $product;
            $result[$id]['price'] = $product->getPrice();
            $result[$id]['height'] = '';
            $result[$id]['width'] = '';
            $result[$id]['depth'] = '';
            $result[$id]['deliveryDate'] = '';
            $result[$id]['count'] = isset($kitCountById[$id]) ? $kitCountById[$id] : 0;

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
            
            $deliveryItems[] = array(
                'id' => $id,
                'quantity' => $result[$id]['count'],
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