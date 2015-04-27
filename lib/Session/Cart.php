<?php

namespace Session;

class Cart {
    /** @var string Сессионное имя новой корзины */
    private $sessionName;
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $products = null;
    /** @var array */
    private $actions = null;
    /** @var int */
    private $productLimit = null;

    public function __construct() {
        $this->sessionName = \App::config()->cart['sessionName'] ?: 'cart';
        $this->storage = \App::session();
        $this->productLimit = \App::config()->cart['productLimit'];

        // очистить старую корзину
        $this->storage->remove('userCart');

        // новый формат корзины
        $data = $this->storage->get($this->sessionName);
        if (!isset($data['product']) || !is_array($data['product'])) {
            $this->storage->set(
                $this->sessionName,
                [
                    'updated' => null,
                    'product' => [],
                ]
            );
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedTime() {
        $data = $this->storage->get($this->sessionName);

        $date = null;
        try {
            $updated = isset($data['updated']) ? $data['updated'] : null;
            if ($updated) {
                $date = new \DateTime($data['updated']);
            }
        } catch (\Exception $e) {}

        return $date;
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return !(bool)$this->getProducts();
    }

    public function clear() {
        $this->products = null;
        $this->actions = null;
        $this->storage->set($this->sessionName, null);
    }

    /**
     * @param \Model\Product\Entity $product
     * @param int $quantity
     * @param array $params Дополнительные параметры товара в корзине
     */
    public function setProduct(\Model\Product\Entity $product, $quantity = 1, array $params = [], $moveProductToUp = false) {
        if ($quantity < 0) $quantity = 0;

         // новый формат
        $data = $this->storage->get($this->sessionName);
        $item = $this->formatProduct($product, $quantity);
        if (!isset($data['product'][$product->getId()]['added'])) {
            $item['added'] = date('c');
        } else {
            $item = array_merge($data['product'][$product->getId()], $item);
        }
        $item += $params;

        // SITE-5022
        if ($moveProductToUp) {
            unset($data['product'][$product->getId()]);
        }

        $data['product'][$product->getId()] = $item;

        if ($quantity == 0 ) unset($data['product'][$product->getId()]);

        $this->update(true);

        $this->storage->set($this->sessionName, $data);
    }

    /**
     * Дополняет данные в сессии для продукта (новая корзина)
     * @param \Model\Product\Entity $product
     */
    public function updateProduct(\Model\Product\Entity $product) {
        $data = $this->storage->get($this->sessionName);
        $item = $data['product'][$product->getId()];

        $item['name']   = $product->getName();
        $item['price']  = $product->getPrice();
        $item['image']  = $product->getImageUrl();
        $item['url']    = $product->getLink();
        $item['category']       = [
            'id'    => $product->getLastCategory() ? $product->getLastCategory()->getId() : null,
            'name'  => $product->getLastCategory() ? $product->getLastCategory()->getName() : null
        ];
        $item['rootCategory']   = [
            'id'    => $product->getMainCategory() ? $product->getMainCategory()->getId() : null,
            'name'  => $product->getMainCategory() ? $product->getMainCategory()->getName() : null
        ];

        $data['product'][$product->getId()] = $item;

        $this->storage->set($this->sessionName, $data);
    }

    /**
     * Возвращает массив данных для продукта в сессии (новая корзина)
     * @param \Model\Product\Entity $product
     * @param $quantity
     * @return array
     */
    public function formatProduct(\Model\Product\Entity $product, $quantity) {
        return [
            'id'            => $product->getId(),
            'ui'            => $product->getUi(),
            'quantity'      => (int)$quantity,
            'name'          => $product->getName(),
            'price'         => $product->getPrice(),
            'image'         => $product->getImageUrl(),
            'url'           => $product->getLink(),
            'rootCategory'  => [
                'id'    => $product->getMainCategory() ? $product->getMainCategory()->getId() : null,
                'name'  => $product->getMainCategory() ? $product->getMainCategory()->getName() : null
            ],
            'category'      => [
                'id'    => $product->getLastCategory() ? $product->getLastCategory()->getId() : null,
                'name'  => $product->getLastCategory() ? $product->getLastCategory()->getName() : null
            ]
        ];
    }

    /**
     * Возвращает продукты новой корзины
     * @return array|null
     */
    public function getProductData() {
        $data = $this->storage->get($this->sessionName);

        return (isset($data['product']) && is_array($data['product'])) ? $data['product'] : [];
    }

    public function update($force = false) {
        $data = $this->storage->get($this->sessionName);

        $productData = (isset($data['product']) && is_array($data['product'])) ? $data['product'] : [];

        $updateMinutes = \App::config()->cart['updateTime'] ?: 1; // время обновления в минутах
        $isUpdated = false;
        $now = new \DateTime('now');
        $updated = $this->getUpdatedTime();

        if (
            $productData
            && (
                $force
                || !$updated
                || ($updated->diff($now, true)->i > $updateMinutes) // больше n-минут
            )
        ) {
            \App::coreClientV2()->addQuery(
                'cart/get-price',
                ['geo_id' => \App::user()->getRegion()->getId()],
                [
                    'product_list'  => array_map(function($item) {
                        return [
                            'id'       => $item['id'],
                            'quantity' => $item['quantity'],
                        ];
                    }, $this->getProductData()),
                ],
                function ($response) use (&$productData, &$data, &$isUpdated) {
                    if (!isset($response['product_list'][0])) return;

                    $data['sum'] = array_key_exists('sum', $response) ? $response['sum'] : 0;
                    $data['originalSum'] = array_key_exists('original_sum', $response) ? $response['original_sum'] : 0;

                    foreach ($response['product_list'] as $item) {
                        $product = (isset($item['id']) && isset($productData[$item['id']])) ? $productData[$item['id']] : null;
                        if (!$product) continue;

                        if (!empty($item['price'])) {
                            $product['price'] = (float)$item['price'];
                            $product['sum'] = (float)$item['sum'];
                            $isUpdated = true;
                        }

                        $productData[$item['id']] = $product;
                    }
                }
            );
            \App::coreClientV2()->execute();

            if ($isUpdated) {
                $data['product'] = $productData;
                $data['updated'] = (new \DateTime('now'))->format('c');
                $this->storage->set($this->sessionName, $data);
            }
        }

        foreach ($productData as $item) {
            $this->products[$item['id']] = new \Model\Cart\Product\Entity($item);
        }

    }

    /**
     * @return array
     */
    public function getProductDump() {
        $products = [];
        $helper = \App::helper();
        foreach ($this->getProductData() as $cartProduct) {

            if (!$cartProduct) { // SITE-4400
                \App::logger()->error(['Товар не найден', 'product' => ['id' => $cartProduct['id']], 'sender' => __FILE__ . ' ' .  __LINE__], ['cart']);

                continue;
            }

            $products[] = [
                'id'                => $cartProduct['id'],
                'name'              => $cartProduct['name'],
                'price'             => $cartProduct['price'],
                'formattedPrice'    => $helper->formatPrice($cartProduct['price']),
                'quantity'          => $cartProduct['quantity'],
                'deleteUrl'         => $helper->url('cart.product.delete', ['productId' => $cartProduct['id']]),
                'link'              => $cartProduct['url'],
                'img'               => $cartProduct['image'],
                'cartButton'        => [ 'id' => \View\Id::cartButtonForProduct($cartProduct['id']), ],
                'category'          => $cartProduct['category'],
                'rootCategory'      => $cartProduct['rootCategory'],
                'isCredit'          => isset($cartProduct['credit']['enabled']) && ($cartProduct['credit']['enabled'] === true)
            ];
        }

        return $products;
    }

    /**
     * @param $productId
     * @return bool
     */
    public function hasProduct($productId) {
        $data = $this->storage->get($this->sessionName) ?: [];

        return array_key_exists($productId, isset($data['product']) ? $data['product'] : []);
    }

    /**
     * Возвращает массив id продуктов, добавленных в кредит (или пустой массив)
     * @return array
     */
    public function getCreditProductIds(){
        $ids = [];
        foreach ((array)$this->getProductData() as $product) {
            if (isset($product['credit']['enabled']) && (true == $product['credit']['enabled'])) $ids[] = $product['id'];
        }
        return $ids;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getQuantityByProduct($productId) {
        $productId = (int)$productId;
        $products = $this->getProductData();

        return isset($products[$productId]['quantity']) ? (int)$products[$productId]['quantity'] : 0;
    }

    /**
     * @return int
     */
    public function getSum() {
        $this->update();

        $data = $this->storage->get($this->sessionName);

        return isset($data['sum']) ? $data['sum'] : 0;
    }

    /**
     * @return int
     */
    public function getOriginalSum() {
        $this->update();

        $data = $this->storage->get($this->sessionName);

        return isset($data['originalSum']) ? $data['originalSum'] : 0;
    }

    /**
     * @return \Model\Cart\Product\Entity[]
     */
    public function getProducts() {
        $this->update();

        return $this->products;
    }

    /**
     * @param int $productId
     * @return \Model\Cart\Product\Entity|null
     */
    public function getProductById($productId) {
        $this->update();

        return isset($this->products[$productId]) ? $this->products[$productId] : null;
    }

    /**
     * @return int
     */
    public function getProductsQuantity() {
        $products = $this->getProductData();

        return count($products);
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->getProducts());
    }
}