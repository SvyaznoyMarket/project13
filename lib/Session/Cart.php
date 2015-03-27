<?php

namespace Session;

class Cart {

    /** @var string */
    private $sessionName;
    /** @var string Сессионное имя новой корзины */
    private $sessionNameNC = 'cart';
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $products = null;
    /** @var \Model\Cart\Service\Entity[]|null */
    private $services = null;
    /** @var \Model\Cart\Certificate\Entity[] */
    private $certificates = null;
    /** @var \Model\Cart\Coupon\Entity[] */
    private $coupons = null;
    /** @var \Model\Cart\Blackcard\Entity[] */
    private $blackcards = null;
    /** @var array */
    private $actions = null;
    /** @var int */
    private $sum = null;
    /** @var int */
    private $originalSum = null;
    /** @var int */
    private $productLimit = null;

    public function __construct() {
        $this->sessionName = \App::config()->cart['sessionName'];
        $this->storage = \App::session();
        $session = $this->storage->all();

        $this->productLimit = \App::config()->cart['productLimit'];

        // если пользователь впервые, то заводим ему пустую корзину
        if (empty($session[$this->sessionName])) {
            $this->storage->set($this->sessionName, [
                'productList'     => [],
                'serviceList'     => [],
                'certificateList' => [],
                'couponList'      => [],
                'blackcardList'   => [],
                'actionData'      => [],
                'paypalProduct'   => [],
            ]);
            return;
        }

        if (!array_key_exists('productList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['productList'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('serviceList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['serviceList'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('certificateList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['certificateList'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('couponList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['couponList'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('blackcardList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['blackcardList'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('actionData', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['actionData'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('paypalProduct', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['paypalProduct'] = [];
            $this->storage->set($this->sessionName, $data);
        }

        // лимит товаров
        $data = $this->storage->get($this->sessionName);
        $productCount = count($data['productList']);
        if ($productCount >= $this->productLimit) {
            $data['productList'] = array_slice($data['productList'], $productCount - $this->productLimit, $this->productLimit, true);
            $this->storage->set($this->sessionName, $data);
            \App::logger()->warn(sprintf('Пользователь sessionId=%s добавил %s-й товар в корзину', $this->storage->getId(), $productCount), ['session', 'cart']);
        }

        // новый формат корзины
        $data = $this->storage->get('cart');
        if (!isset($data['product']) || !is_array($data['product'])) {
            $this->storage->set('cart', [
                'product' => [],
            ]);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return !(bool)$this->getProducts() && !(bool)$this->getServices();
    }

    public function clear() {
        $this->storage->set($this->sessionName, null);
        $this->sum = null;
        $this->products = null;
        $this->services = null;
        $this->certificates = null;
        $this->coupons = null;
        $this->blackcards = null;
        $this->actions = null;

        // MSITE-78 для мобильного сайта
        $this->storage->set('cart', null);
    }

    public function clearPaypal() {
        $data = $this->storage->get($this->sessionName);
        $data['paypalProduct'] = [];

        $this->storage->set($this->sessionName, $data);
    }

    /**
     * @param \Model\Product\Entity $product
     * @param int $quantity
     * @param array $params Дополнительные параметры товара в корзине
     */
    public function setProduct(\Model\Product\Entity $product, $quantity = 1, array $params = [], $moveProductToUp = false) {
        if ($quantity < 0) $quantity = 0;

        $data = $this->storage->get($this->sessionName, []);
        $data['productList'][$product->getId()] = (int)$quantity;

        $this->storage->set($this->sessionName, $data);
        $this->clearEmpty();

        // новый формат
        $data = $this->storage->get($this->sessionNameNC);
        $item = $this->formatProductNC($product, $quantity);
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
        $this->storage->set($this->sessionNameNC, $data);
    }

    /** Дополняет данные в сессии для продукта (новая корзина)
     * @param \Model\Product\Entity $product
     */
    public function updateProductNC(\Model\Product\Entity $product) {
        $data = $this->storage->get($this->sessionNameNC);
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

        $this->storage->set($this->sessionNameNC, $data);
    }

    /** Возвращает массив данных для продукта в сессии (новая корзина)
     * @param \Model\Product\Entity $product
     * @param $quantity
     * @return array
     */
    public function formatProductNC(\Model\Product\Entity $product, $quantity) {
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

    /** Возвращает данные новой корзины
     * @return array|null
     */
    public function getCartNC() {
        return $this->storage->get($this->sessionNameNC);
    }

    /** Возвращает продукты новой корзины
     * @return array|null
     */
    public function getProductsNC(){
        $data = $this->storage->get($this->sessionNameNC);
        return isset($data['product']) ? $data['product'] : null;
    }

    public function getProductsDumpNC() {
        $products = [];
        $helper = \App::helper();
        foreach ($this->getProductsNC() as $cartProduct) {

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
        $data = $this->storage->get($this->sessionName);

        return array_key_exists($productId, $data['productList']);
    }

    /** Возвращает массив id продуктов, добавленных в кредит (или пустой массив)
     * @return array
     */
    public function getCreditProductIds(){
        $ids = [];
        foreach ((array)$this->getProductsNC() as $product) {
            if (isset($product['credit']['enabled']) && (true == $product['credit']['enabled'])) $ids[] = $product['id'];
        }
        return $ids;
    }

    public function shiftProduct() {
        $data = $this->storage->get($this->sessionName);
        reset($data['productList']);

        $key = key($data['productList']);
        unset($data['productList'][$key]);
        $this->storage->set($this->sessionName, $data);
    }

    /**
     * @param \Model\Product\Service\Entity $service
     * @param int $quantity
     * @param int|null $productId
     */
    public function setService(\Model\Product\Service\Entity $service, $quantity = 1, $productId = null) {
        if ($quantity < 0) $quantity = 0;

        if (!$productId) {
            $productId = 0;
        }

        $data = $this->storage->get($this->sessionName);
        if (!array_key_exists($service->getId(), $data['serviceList'])) {
            $data['serviceList'][$service->getId()] = [];
        }
        $data['serviceList'][$service->getId()][$productId] = $quantity;

        $this->storage->set($this->sessionName, $data);
        $this->clearEmpty();
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getQuantityByProduct($productId) {
        $data = $this->getData();
        $productId = (int)$productId;

        if (array_key_exists($productId, $data['productList'])) {
            return $data['productList'][$productId];
        }

        return 0;
    }

    /** Возвращает количество продуктов и сервисов
     * @return int
     */
    public function count() {
        $count = 0;
        $data = $this->getData();
        foreach ($data['serviceList'] as $service) {
            foreach ($service as $quantity) {
                $count += $quantity;
            }
        }
        foreach ($data['productList'] as $quantity) {
            $count += $quantity;
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getTotalProductPrice() {
        $price = 0;
        foreach ($this->getProducts() as $product) {
            $price += $product->getSum();
        }

        return $price;
    }

    /**
     * @return int
     */
    public function getTotalServicePrice() {
        $price = 0;
        foreach ($this->getProducts() as $product) {
            foreach ($product->getService() as $service) {
                $price += $service->getSum();
            }
        }
        foreach ($this->getServices() as $service) {
            $price += $service->getPrice();
        }

        return $price;
    }

    /**
     * @return int
     */
    public function getSum() {
        if (null === $this->sum) {
            $this->fill();
        }

        return $this->sum;
    }

    /**
     * @return int
     */
    public function getOriginalSum() {
        if (null === $this->originalSum) {
            $this->fill();
        }

        return $this->originalSum;
    }

    /**
     * @return \Model\Cart\Product\Entity[]
     */
    public function getProducts() {
        if (null === $this->products) {
            $this->fill();
        }

        return $this->products;
    }

    /**
     * @param int $productId
     * @return \Model\Cart\Product\Entity|null
     */
    public function getProductById($productId) {
        if (null === $this->products) {
            $this->fill();
        }

        return isset($this->products[$productId]) ? $this->products[$productId] : null;
    }

    /**
     * @return \Model\Cart\Service\Entity[]
     */
    public function getServices() {
        if (null === $this->services) {
            $this->fill();
        }

        return $this->services;
    }

    /**
     * @return bool
     */
    public function hasServices() {
        $data = $this->getData();

        return count($data['serviceList']) > 0;
    }

    /**
     * @param int $serviceId
     * @return \Model\Cart\Service\Entity|null
     */
    public function getServiceById($serviceId) {
        if (null === $this->services) {
            $this->fill();
        }

        return isset($this->services[$serviceId]) ? $this->services[$serviceId] : null;
    }

    /**
     * @return int
     */
    public function getProductsQuantity() {
        $data = $this->getData();

        return count($data['productList']);
    }

    /**
     * @return int
     */
    public function getServicesQuantity() {
        $data = $this->getData();

        return count($data['serviceList']);
    }

    /**
     * @param int|null $productId
     * @return int
     */
    public function getServicesQuantityByProduct($productId) {
        $count = 0;
        $data = $this->getData();

        foreach ($data['serviceList'] as $service) {
            if (array_key_exists($productId, $service)) {
                $count++;
            }
        }

        return $count;
    }

    public function getData() {
        return $this->storage->get($this->sessionName);
    }

    /**
     * @param \Model\Cart\Product\Entity $product
     */
    public function setPaypalProduct(\Model\Cart\Product\Entity $product) {
        $data = $this->storage->get($this->sessionName);
        $data['paypalProduct'] = [];
        $data['paypalProduct'][$product->getId()] = [
            'id'          => $product->getId(),
            'quantity'    => $product->getQuantity(),
            'sum'         => $product->getSum(),
            'deliverySum' => $product->getDeliverySum(),
        ];

        $this->storage->set($this->sessionName, $data);
        $this->clearEmpty();
    }

    /**
     * @return \Model\Cart\Product\Entity|null
     */
    public function getPaypalProduct() {
        $data = $this->storage->get($this->sessionName);
        $item = reset($data['paypalProduct']);

        return is_array($item) ? new \Model\Cart\Product\Entity($item) : null;
    }

    /**
     * @return array
     */
    public function getProductData() {
        $data = $this->getData();
        $return = [];
        foreach ($data['productList'] as $productId => $productQuantity) {
            $return[] = [
                'id'       => $productId,
                'quantity' => $productQuantity
            ];
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getServiceData() {
        $data = $this->getData();
        $return = [];
        foreach ($data['serviceList'] as $serviceId => $serviceData) {
            foreach ($serviceData as $productId => $serviceQuantity) {
                $productId = (int)$productId;
                $item = [
                    'id'       => $serviceId,
                    'quantity' => $serviceQuantity
                ];

                if ($productId > 0) {
                    $item['product_id'] = $productId;
                }
                $return[] = $item;
            }
        }

        return $return;
    }

    /**
     * @param \Model\Cart\Certificate\Entity $certificate
     */
    public function setCertificate(\Model\Cart\Certificate\Entity $certificate) {
        $this->clearCertificates(); // возможно активировать только один сертификат

        $data = $this->storage->get($this->sessionName);
        $data['certificateList'][] = [
            'number' => $certificate->getNumber(),
        ];
        $this->certificates[] = $certificate;

        $this->fill();

        $this->storage->set($this->sessionName, $data);
    }

    public function clearCertificates() {
        $data = $this->storage->get($this->sessionName);
        $data['certificateList'] = [];
        $this->certificates = null;

        $this->fill();

        $this->storage->set($this->sessionName, $data);
    }

    /**
     * @return \Model\Cart\Certificate\Entity[]
     */
    public function getCertificates() {
        if (null === $this->certificates) {
            $data = $this->storage->get($this->sessionName);
            foreach ($data['certificateList'] as $certificateData) {
                $this->certificates[$certificateData['number']] = new \Model\Cart\Certificate\Entity($certificateData);
            }
        }

        return $this->certificates ?: [];
    }

    /**
     * @param \Model\Cart\Coupon\Entity $coupon
     */
    public function setCoupon(\Model\Cart\Coupon\Entity $coupon) {
        $this->clearCoupons(); // возможно активировать только один купон

        $data = $this->storage->get($this->sessionName);
        $data['couponList'][] = [
            'number' => $coupon->getNumber(),
        ];
        $this->coupons[] = $coupon;

        $this->fill();

        $this->storage->set($this->sessionName, $data);
    }

    public function clearCoupons() {
        $data = $this->storage->get($this->sessionName);
        $data['couponList'] = [];

        if (is_array($data['actionData'])) {
            foreach ($data['actionData'] as $key => $actionDataItem) {
                if (isset($actionDataItem['type']) && $actionDataItem['type'] === 'coupon') {
                    unset($data['actionData'][$key]);
                }
            }

            $data['actionData'] = array_values($data['actionData']);
        }

        $this->coupons = null;
        $this->storage->set($this->sessionName, $data);
        $this->fill();
    }

    /**
     * @return \Model\Cart\Coupon\Entity[]
     */
    public function getCoupons() {
        if (null === $this->coupons) {
            $data = $this->storage->get($this->sessionName);
            foreach ($data['couponList'] as $couponData) {
                $this->coupons[$couponData['number']] = new \Model\Cart\Coupon\Entity($couponData);
            }
        }

        return $this->coupons ?: [];
    }

    /**
     * @param \Model\Cart\Blackcard\Entity $blackcard
     */
    public function setBlackcard(\Model\Cart\Blackcard\Entity $blackcard) {
        $this->clearBlackcards(); // возможно активировать только одну черную карту

        $data = $this->storage->get($this->sessionName);
        $data['blackcardList'][] = [
            'number' => $blackcard->getNumber(),
        ];
        $this->blackcards[] = $blackcard;

        $this->fill();

        $this->storage->set($this->sessionName, $data);
    }

    public function clearBlackcards() {
        $data = $this->storage->get($this->sessionName);
        $data['blackcardList'] = [];
        $this->blackcards = [];

        $this->fill();

        $this->storage->set($this->sessionName, $data);
    }

    /**
     * @return \Model\Cart\Blackcard\Entity[]
     */
    public function getBlackcards() {
        if (null === $this->blackcards) {
            $data = $this->storage->get($this->sessionName);
            foreach ($data['blackcardList'] as $blackcardData) {
                $this->blackcards[$blackcardData['number']] = new \Model\Cart\Blackcard\Entity($blackcardData);
            }
        }

        return $this->blackcards ?: [];
    }

    /**
     * Костылище для ядра
     *
     * @param array $newActionData
     */
    public function setActionData(array $newActionData) {
        try {
            $data = $this->storage->get($this->sessionName);
            \App::logger()->info(['action' => __METHOD__,  'cart.actionData' => $data['actionData']], ['cart']);

            $actionDataCopy = $data['actionData'];
            foreach ($newActionData as $newActionDataItem) {
                if (!isset($newActionDataItem['id'])) {
                    continue;
                }

                if (!isset($newActionDataItem['product_list'])) {
                    $newActionDataItem['product_list'] = [];
                }

                foreach ($actionDataCopy as $i => $actionDataCopyItem) {
                    if (isset($actionDataCopyItem['id']) && $actionDataCopyItem['id'] === $newActionDataItem['id']) {
                        unset($data['actionData'][$i]);
                    }
                }

                $data['actionData'][] = $newActionDataItem;
            }

            $data['actionData'] = array_values($data['actionData']);
            $this->actions = $data['actionData'];

            \App::logger()->info(['action' => __METHOD__, 'cart.actionData' => $data['actionData']], ['cart']);

            $this->storage->set($this->sessionName, $data);
        } catch(\Exception $e) {
            \App::logger()->error(['message' => $e->getMessage(), 'action' => __METHOD__], ['cart']);
        }
    }

    /**
     * @return array
     */
    public function getActionData() {
        $data = $this->storage->get($this->sessionName);

        if (!empty($data['actionData'])) {
            $this->actions = $data['actionData'];
        } elseif (null === $this->actions) {
            $this->fill();
        }

        return $this->actions ?: [];
    }

    public function clearActionData() {
        $data = $this->storage->get($this->sessionName);
        $data['actionData'] = [];
        $this->storage->set($this->sessionName, $data);
    }

    public function fill() {
        // получаем список цен
        $default = [
            'product_list'   => [],
            'service_list'   => [],
            'card_f1_list'   => [],
            'coupon_list'    => [],
            'blackcard_list' => [],
            'price_total'    => 0,
        ];

        try {
            $response = $default;

            // если в корзине есть товары или услуги
            if (((bool)$this->getProductsQuantity() || (bool)$this->getServicesQuantity())) {
                // сертификат
                $certificates = $this->getCertificates();
                $certificate = is_array($certificates) ? reset($certificates) : null;

                // купоны
                $coupons = $this->getCoupons();
                $coupon = is_array($coupons) ? reset($coupons) : null;

                // черные карты
                $blackcards = $this->getBlackcards();
                $blackcard = is_array($blackcards) ? reset($blackcards) : null;

                \App::coreClientV2()->addQuery(
                    'cart/get-price',
                    ['geo_id' => \App::user()->getRegion()->getId()],
                    [
                        'product_list'  => $this->getProductData(),
                        'service_list'  => $this->getServiceData(),
                    ],
                    function ($data) use (&$response) {
                        if ((bool)$data) {
                            $response = $data;
                        }
                    }
                );
                \App::coreClientV2()->execute();
            }
        } catch(\Exception $e) {
            \App::logger()->error($e, ['session', 'cart']);
            $response = $default;
        }

        $this->sum = array_key_exists('sum', $response) ? $response['sum'] : 0;
        $this->originalSum = array_key_exists('original_sum', $response) ? $response['original_sum'] : 0;

        if (array_key_exists('action_list', $response)) {
            $this->setActionData((array)$response['action_list']);
        }

        $this->certificates = [];
        if (array_key_exists('card_f1_list', $response)) {
            foreach ($response['card_f1_list'] as $certificateData) {
                $this->certificates[$certificateData['number']] = new \Model\Cart\Certificate\Entity($certificateData);
            }
        }

        $this->coupons = [];
        if (array_key_exists('coupon_list', $response)) {
            foreach ($response['coupon_list'] as $couponData) {
                if (isset($couponData['error']) && (bool)$couponData['error']) {
                    \App::logger()->error($couponData['error'], ['cart']);
                }
                $this->coupons[$couponData['number']] = new \Model\Cart\Coupon\Entity($couponData);
            }
        }

        /*
        $this->blackcards = [];
        if (array_key_exists('blackcard_list', $response)) {
            foreach ($response['blackcard_list'] as $blackcardData) {
                if (isset($blackcardData['error']) && (bool)$blackcardData['error']) {
                    \App::logger()->error($blackcardData['error'], ['cart']);
                }
                $this->blackcards[$blackcardData['number']] = new \Model\Cart\Blackcard\Entity($blackcardData);
            }
        }
        */

        $this->products = [];
        if (array_key_exists('product_list', $response)) {
            foreach ($response['product_list'] as $productData) {
                $this->products[$productData['id']] = new \Model\Cart\Product\Entity($productData);
            }
        }

        $this->services = [];
        if (array_key_exists('service_list', $response)) {
            foreach ($response['service_list'] as $serviceData) {
                $service = new \Model\Cart\Service\Entity($serviceData);
                $productId = (array_key_exists('product_id', $serviceData)) ? (int)$serviceData['product_id'] : null;
                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = $productId && isset($this->products[$productId]) && ($this->products[$productId] instanceof \Model\Cart\Product\Entity)
                    ? $this->products[$productId]
                    : null;
                if ($cartProduct) {
                    $cartProduct->addService($service);
                } else {
                    $this->services[$serviceData['id']] = $service;
                }
            }
        }

    }

    /**
     * Удаляет товары, услуги и гарантии с нулевым количеством
     */
    private function clearEmpty() {
        $data = $this->storage->get($this->sessionName);

        // товары
        foreach ($data['productList'] as $productId => $quantity) {
            if (!$quantity) {
                unset($data['productList'][$productId]);
            }
        }
        // услуги
        foreach ($data['serviceList'] as $serviceId => $serviceData) {
            foreach ($serviceData as $productId => $quantity) {
                if (
                    ($productId && !array_key_exists($productId, $data['productList']))
                    || !$quantity
                ) {
                    unset($data['serviceList'][$serviceId][$productId]);
                }

                if (!(bool)$data['serviceList'][$serviceId]) {
                    unset($data['serviceList'][$serviceId]);
                }
            }
        }
        // товары, оплачиваемые через PayPal
        $item = reset($data['paypalProduct']);
        if (isset($item['quantity']) && !$item['quantity']) {
            $data['paypalProduct'] = [];
        }

        $this->storage->set($this->sessionName, $data);
    }
}