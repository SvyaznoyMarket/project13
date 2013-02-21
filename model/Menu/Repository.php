<?php

namespace Model\Menu;

class Repository {
    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }

    /**
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query('/main-menu.json');

        $collection = [];
        foreach ($data as $item) {
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    public function setEntityLink(Entity $entity, \Routing\Router $router, $productsById = [], $categoriesById = [], $servicesById = []) {
        $link = null;

        try {
            $items = $entity->getItem();
            if (!(bool)$items) {
                throw new \Exception('Пустые элементы для действия у меню');
            }

            switch ($entity->getAction()) {
                case Entity::ACTION_LINK:
                    $link = is_array($items) ? reset($items) : (string)$items;
                    break;
                case Entity::ACTION_PRODUCT:
                    $products = [];
                    foreach ($items as $id) {
                        if (!isset($productsById[$id])) {
                            \App::logger()->error(sprintf('Для промо-каталога не найден товар #%s', $id));
                            continue;
                        }
                        $products[] = $productsById[$id];
                    }

                    if (1 == count($items)) {
                        $product = reset($products);
                        $link = $router->generate('product', array('productPath' => $product->getPath()));;
                    } else {
                        $barcodes = array_map(function ($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $products);
                        $link = $router->generate('product.set', array('productBarcodes' => implode(',', $barcodes)));
                    }

                    break;
                case Entity::ACTION_PRODUCT_CATEGORY:
                    $id = reset($items);
                    /** @var $category \Model\Product\Category\Entity */
                    $category = ($id && isset($categoriesById[$id])) ? $categoriesById[$id] : null;
                    if ($category) {
                        $link = $router->generate('product.category', array('categoryPath' => $category->getPath()));;
                    } else {
                        \App::logger()->error(sprintf('Для промо-каталога не найдена категория товара #%s', $id));
                    }

                    break;
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $entity->setLink($link);
    }
}