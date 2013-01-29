<?php

namespace Model\Promo;

class Repository {
    /**
     * @param \DataStore\Client $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }

    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $data = $this->client->query(sprintf('promo/%s/index.json', $token));
        if (is_array($data)) {
            $data['token'] = $token;
        }

        return (bool)$data ? new Entity($data) : null;
    }

    /**
     * @param Image\Entity $image
     * @param \Routing\Router $router
     * @param \Model\Product\Entity[] $productsById
     * @param \Model\Product\Service\Entity[] $servicesById
     * @param \Model\Product\Category\Entity[] $categoriesById
     * @throws \Exception
     */
    public function setEntityImageLink(Image\Entity $image, \Routing\Router $router, $productsById = [], $categoriesById = [], $servicesById = []) {
        $link = null;

        try {
            $items = $image->getItem();
            if (!(bool)$items) {
                throw new \Exception(sprintf('Пустые элементы картинки %s у промо-каталога', $image->getUrl()));
            }

            switch ($image->getAction()) {
                case Image\Entity::ACTION_LINK:
                    $link = is_array($items) ? reset($items) : (string)$items;
                    break;
                case Image\Entity::ACTION_PRODUCT:
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
                case Image\Entity::ACTION_PRODUCT_CATEGORY:
                    $id = reset($items);
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

        $image->setLink($link);
    }
}