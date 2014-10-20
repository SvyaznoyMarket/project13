<?php

namespace Session;

class Cart {

    /** @var string */
    private $sessionName;
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $products = null;
    /** @var \Model\Cart\Service\Entity[]|null */
    private $services = null;
    /** @var \Model\Cart\Warranty\Entity[]|null */
    private $warranties = null;
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
                'warrantyList'    => [],
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

        if (!array_key_exists('warrantyList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['warrantyList'] = [];
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
        $this->warranties = null;
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
     */
    public function setProduct(\Model\Product\Entity $product, $quantity = 1) {
        if ($quantity < 0) $quantity = 0;

        $data = $this->storage->get($this->sessionName, []);
        $data['productList'][$product->getId()] = $quantity;

        $this->storage->set($this->sessionName, $data);
        $this->clearEmpty();
    }

    /**
     * @param $productId
     * @return bool
     */
    public function hasProduct($productId) {
        $data = $this->storage->get($this->sessionName);

        return array_key_exists($productId, $data['productList']);
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

    /**
     * @param \Model\Product\Warranty\Entity $warranty
     * @param int $quantity
     * @param int $productId
     * @throws \InvalidArgumentException
     */
    public function setWarranty(\Model\Product\Warranty\Entity $warranty, $quantity = 1, $productId) {
        if ($quantity < 0) $quantity = 0;

        if (empty($productId)) {
            throw new \InvalidArgumentException(sprintf('Пустой ид товара для гарантии #%s', $warranty->getId()));
        }

        $data = $this->storage->get($this->sessionName);
        if (!array_key_exists($warranty->getId(), $data['warrantyList'])) {
            $data['warrantyList'][$warranty->getId()] = [];
        }
        // удаляем ранее установленную для товара гарантию
        foreach ($data['warrantyList'] as $iWarrantyId => $warrantyData) {
            foreach ($warrantyData as $iProductId => $iQuantity) {
                if ($iProductId == $productId) {
                    $data['warrantyList'][$iWarrantyId][$iProductId] = 0;
                }
            }
        }

        $data['warrantyList'][$warranty->getId()][$productId] = $quantity;

        $this->storage->set($this->sessionName, $data);
        $this->clearEmpty();
    }

    /**
     * @param int $productId
     * @param int $warrantyId
     * @return bool
     */
    public function hasWarranty($productId, $warrantyId) {
        $data = $this->getData();

        return isset($data['warrantyList'][$warrantyId][$productId]);
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
    public function getTotalWarrantyPrice() {
        $price = 0;
        foreach ($this->getProducts() as $product) {
            foreach ($product->getWarranty() as $warranty) {
                $price += $warranty->getSum();
            }
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
     * @return \Model\Cart\Warranty\Entity[]
     */
    public function getWarranties() {
        if (null === $this->warranties) {
            $this->fill();
        }

        return $this->warranties;
    }

    /**
     * @param int $warrantyId
     * @return \Model\Cart\Warranty\Entity|null
     */
    public function getWarrantyById($warrantyId) {
        if (null === $this->warranties) {
            $this->fill();
        }

        return isset($this->warranties[$warrantyId]) ? $this->warranties[$warrantyId] : null;
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
     * @return int
     */
    public function getWarrantiesQuantity() {
        $data = $this->getData();

        return count($data['warrantyList']);
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
     * @return array
     */
    public function getWarrantyData() {
        $data = $this->getData();
        $return = [];
        foreach($data['warrantyList'] as $warrantyId => $warrantyData) {
            foreach($warrantyData as $productId => $quantity) {
                $return[] = [
                    'id'         => $warrantyId,
                    'quantity'   => $quantity,
                    'product_id' => (int)$productId,
                ];
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
            'warranty_list'  => [],
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

                if (
                    (\App::config()->f1Certificate['enabled'] && $certificate instanceof \Model\Cart\Certificate\Entity)
                    || (\App::config()->coupon['enabled'] && $coupon instanceof \Model\Cart\Coupon\Entity)
                    || (\App::config()->blackcard['enabled'] && $blackcard instanceof \Model\Cart\Blackcard\Entity)
                ) {
                    $data = [
                        'user_id'       => \App::user()->getEntity() ? \App::user()->getEntity()->getId() : 0,
                        'timestamp'     => time(),
                        'product_list'  => $this->getProductData(),
                        'service_list'  => $this->getServiceData(),
                        'warranty_list' => $this->getWarrantyData(),
                        'card_f1_list'  =>
                        $certificate
                            ? [
                                ['number' => $certificate->getNumber()],
                            ]
                            : [],
                        'coupon_list'   =>
                            $coupon
                            ? [
                                ['number' => $coupon->getNumber()],
                            ]
                            : [],
                        'blackcard_list' =>
                            $blackcard
                            ? [
                                ['number' => $blackcard->getNumber()],
                            ]
                            : [],
                    ];

                    $isFailed = false;
                    \App::coreClientV2()->addQuery(
                        'cart/get-price-new',
                        [
                            'geo_id'    => \App::user()->getRegion()->getId(),
                        ],
                        $data,
                        function ($data) use (&$response) {
                            if ((bool)$data) {
                                $response = $data;
                            }
                        },
                        function(\Exception $e) use (&$isFailed) {
                            $isFailed = true;
                        }
                    );
                    \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['long']);
                    \App::logger()->info(['core.response' => $response], ['cart']);

                    // если запрос со скидками провалился, используем обычный запрос
                    if ($isFailed) {
                        \App::coreClientV2()->addQuery(
                            'cart/get-price',
                            ['geo_id' => \App::user()->getRegion()->getId()],
                            [
                                'product_list'  => $this->getProductData(),
                                'service_list'  => $this->getServiceData(),
                                'warranty_list' => $this->getWarrantyData(),
                            ],
                            function ($data) use (&$response) {
                                if ((bool)$data) {
                                    $response = $data;
                                }
                            }
                        );
                        \App::coreClientV2()->execute();
                    }
                } else {
                    \App::coreClientV2()->addQuery(
                        'cart/get-price',
                        ['geo_id' => \App::user()->getRegion()->getId()],
                        [
                            'product_list'  => $this->getProductData(),
                            'service_list'  => $this->getServiceData(),
                            'warranty_list' => $this->getWarrantyData(),
                        ],
                        function ($data) use (&$response) {
                            if ((bool)$data) {
                                $response = $data;
                            }
                        }
                    );
                    \App::coreClientV2()->execute();
                }
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

        $this->warranties = [];
        if(array_key_exists('warranty_list', $response)) {
            foreach($response['warranty_list'] as $warrantyData) {
                $warranty = new \Model\Cart\Warranty\Entity($warrantyData);
                $productId = (array_key_exists('product_id', $warrantyData)) ? (int)$warrantyData['product_id'] : null;
                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = $productId && isset($this->products[$productId]) && ($this->products[$productId] instanceof \Model\Cart\Product\Entity)
                    ? $this->products[$productId]
                    : null;
                if ($cartProduct) {
                    $cartProduct->addWarranty($warranty);
                } else {
                    $this->services[$warrantyData['id']] = $warranty;
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
        // гарантии
        foreach($data['warrantyList'] as $warrantyId => $warrantyData) {
            foreach($warrantyData as $productId => $quantity) {
                if (
                    ($productId && !array_key_exists($productId, $data['productList']))
                    || !$quantity
                ) {
                    unset($data['warrantyList'][$warrantyId][$productId]);
                }

                if (!(bool)$data['warrantyList'][$warrantyId]) {
                    unset($data['warrantyList'][$warrantyId]);
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