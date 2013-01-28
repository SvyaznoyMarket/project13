<?php

namespace Model\Banner;

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
     * @param \Model\Region\Entity|null $region
     * @return Entity[]
     */
    public function getCollection(\Model\Region\Entity $region = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args()));

        $params = array();
        if ($region) {
            $params['geo_id'] = $region->getId();
        }
        $data = $this->client->query('promo/get');

        $collection = array();
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param \Model\Region\Entity $region
     * @param                      $callback
     */
    public function prepareCollection(\Model\Region\Entity $region = null, $callback) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = array();
        if ($region) {
            $params['geo_id'] = $region->getId();
        }
        $this->client->addQuery('promo/get', $params, array(), $callback);
    }

    /**
     * @param Entity $entity
     * @param \Routing\Router $router
     * @param \Model\Product\Entity[] $productsById
     * @param \Model\Product\Service\Entity[] $servicesById
     * @param \Model\Product\Category\Entity[] $categoriesById
     * @throws \Exception
     */
    public function setEntityUrl(Entity $entity, \Routing\Router $router, $productsById = [], $categoriesById = [], $servicesById = []) {
        $url = $entity->getUrl();
        if (!empty($url)) {
            return;
        }

        try {
            $items = $entity->getItem();
            /** @var $item \Model\Banner\Item\Entity */
            $item = reset($items);
            if (!$item) {
                return;
            }

            if ($item->getProductId()) {
                $products = array();
                foreach ($items as $item) {
                    $product = isset($productsById[$item->getProductId()])
                        ? $productsById[$item->getProductId()]
                        : null;
                    if (!$product) {
                        throw new \Exception(sprintf('Товар #%s для баннера #%s не получен', $item->getProductId(), $entity->getId()));
                    }

                    $products[] = $product;
                }
                if (!(bool)$products) {
                    throw new \Exception(sprintf('Товары для баннера #%s не получены', $entity->getId()));
                }

                if (1 == count($products)) {
                    /** @var $product \Model\Product\Entity */
                    $product = reset($products);
                    $url = $router->generate('product', array('productPath' => $product->getPath()));
                } else {
                    $barcodes = array_map(function ($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $products);
                    $url = $router->generate('product.set', array(
                        'productBarcodes' => implode(',', $barcodes),
                    ));
                }
            } else if ($item->getServiceId()) {
                \App::logger()->error('Услуги для баннера еще не реализованы');
            } else if ($item->getProductCategoryId()) {
                /** @var $product \Model\Product\Category\Entity */
                $category = ($item->getProductCategoryId() && isset($categoriesById[$item->getProductCategoryId()]))
                    ? $categoriesById[$item->getProductCategoryId()]
                    : null;
                if (!$category) {
                    throw new \Exception(sprintf('Категория #%s не найдена', $item->getProductCategoryId()));
                }

                $url = $router->generate('product.category', array('categoryPath' => $category->getPath()));
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $entity->setUrl($url);
    }
}