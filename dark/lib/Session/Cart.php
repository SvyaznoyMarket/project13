<?php

namespace Session;

class Cart {

    /** @var string */
    private $sessionName = 'userCart';
    /** @var \Http\Session */
    private $storage;
    /** @var \Model\Cart\Product\Entity[]|null */
    private $products = null;
    /** @var \Model\Cart\Service\Entity[]|null */
    private $services = null;
    /** @var \Model\Cart\Warranty\Entity[]|null */
    private $warranties = null;
    /** @var int|null */
    private $totalPrice = null;

    public function __construct() {
        $this->storage = \App::session();
        $session = $this->storage->all();

        /**
         * Если человек впервые - заводим ему пустую корзину
         */
        if(!array_key_exists($this->sessionName, $session)){
            $this->storage->set($this->sessionName, array('productList' => array(), 'serviceList' => array(), 'warrantyList' => array()));
            return;
        }

        if (!array_key_exists('productList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['productList'] = array();
            $this->storage->set($this->sessionName, $data);
        }

        if (!array_key_exists('serviceList', $session[$this->sessionName])) {
            $data = $this->storage->get($this->sessionName);
            $data['serviceList'] = array();
            $this->storage->set($this->sessionName, $data);
        }

        if(!array_key_exists('warrantyList', $session[$this->sessionName])){
            $data = $this->storage->get($this->sessionName);
            $data['warrantyList'] = array();
            $this->storage->set($this->sessionName, $data);
        }

    }

    public function hasProduct($productId) {
        $session = $this->storage->all();
        return array_key_exists($productId, $session[$this->sessionName]['productList']);
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getQuantityByProduct($productId) {
        $session = $this->storage->all();
        $productId = (int)$productId;

        if (array_key_exists($productId, $session[$this->sessionName]['productList'])) {
            if ((int)$session[$this->sessionName]['productList'][$productId] < 1) {
                unset($session[$this->sessionName]['productList'][$productId]);

                return 0;
            }

            return $session[$this->sessionName]['productList'][$productId];
        }

        return 0;
    }

    /**
     * TODO: переделать чтобы отдавала сущности \Model\Cart\Warranty\Entity
     * @param $productId
     * @return array
     */
    public function getWarrantyByProduct($productId) {
        $return = array();
        $session = $this->storage->all();

        foreach ($session[$this->sessionName]['warrantyList'] as $warrantyId => $warranty) {
            if (array_key_exists($productId, $warranty)) {
                $return[] = $warrantyId;
            }
        }

        return array_unique($return);
    }

    /**
     * @param int $productId
     * @param int $warrantyId
     * @return bool
     */
    public function hasWarranty($productId, $warrantyId) {
        $session = $this->storage->all();
        return isset($session[$this->sessionName]['warrantyList'][$warrantyId][$productId]);
    }

    /**
     * @return int
     */
    public function count() {
        $count = 0;
        $session = $this->storage->all();
        foreach ($session[$this->sessionName]['serviceList'] as $service) {
            foreach ($service as $quantity) {
                $count += $quantity;
            }
        }
        foreach ($session[$this->sessionName]['productList'] as $quantity) {
            $count += $quantity;
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getTotalProductPrice() {
        $price = 0;
        foreach($this->getProducts() as $product){
            $price += $product->getTotalPrice();
        }

        return $price;
    }

    /**
     * @return int
     */
    public function getTotalPrice() {
        if (null === $this->totalPrice) {
            $this->fill();
        }

        return $this->totalPrice;
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
        $session = $this->storage->all();

        return count($session[$this->sessionName]['productList']);
    }

    /**
     * @return int
     */
    public function getServicesQuantity() {
        $session = $this->storage->all();

        return count($session[$this->sessionName]['serviceList']);
    }

    /**
     * @param int|null $productId
     * @return int
     */
    public function getServicesQuantityByProduct($productId) {
        $count = 0;
        $session = $this->storage->all();

        foreach ($session[$this->sessionName]['serviceList'] as $service) {
            if (array_key_exists($productId, $service)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array
     */
    public function getWarrantiesQuantity() {
        $session = $this->storage->all();
        $return = array();

        foreach($session[$this->sessionName]['warrantyList'] as $warrantyId => $warrantiesByProduct) {
            foreach ($warrantiesByProduct as $productId => $warrantyQuantity) {
                $data =array(
                    'id'         => $warrantyId,
                    'quantity'   => $warrantyQuantity,
                    'product_id' => (int)$productId,
                );

                $return[] = $data;
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getProductData() {
        $session = $this->storage->all();
        $return = array();
        foreach ($session[$this->sessionName]['productList'] as $productId => $productQuantity) {
            $return[] = array(
                'id'       => $productId,
                'quantity' => $productQuantity
            );
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getServiceData() {
        $session = $this->storage->all();
        $return = array();
        foreach ($session[$this->sessionName]['serviceList'] as $serviceId => $serviceList) {
            foreach ($serviceList as $productId => $serviceQuantity) {
                $productId = (int)$productId;
                $data = array(
                    'id'       => $serviceId,
                    'quantity' => $serviceQuantity
                );

                if ($productId > 0) {
                    $data['product_id'] = $productId;
                }
                $return[] = $data;
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getWarrantyData() {
        $session = $this->storage->all();
        $return = array();
        foreach($session[$this->sessionName]['warrantyList'] as $warrantyId => $warrantiesByProduct) {
            foreach($warrantiesByProduct as $productId => $warrantyQuantity){
                $data =array(
                    'id'         => $warrantyId,
                    'quantity'   => $warrantyQuantity,
                    'product_id' => (int)$productId,
                );
                $return[] = $data;
            }
        }

        return $return;
    }

    public function fill() {
        // получаем список цен
        $default = array(
            'product_list'  => array(),
            'service_list'  => array(),
            'warranty_list' => array(),
            'price_total'   => 0,
        );

        try {
            if (((bool)$this->getProductsQuantity() || (bool)$this->getServicesQuantity())) {
                $response = \App::coreClientV2()->query(
                    'cart/get-price',
                    array('geo_id' => \App::user()->getRegion()->getId()),
                    array(
                        'product_list'  => $this->getProductData(),
                        'service_list'  => $this->getServiceData(),
                        'warranty_list' => $this->getWarrantyData(),
                    ));
            } else {
                $response = $default;
            }
        } catch(\Exception $e) {
            \App::logger()->error($e);
            $response = $default;
        }

        $this->totalPrice = array_key_exists('price_total', $response) ? $response['price_total'] : 0;

        $this->products = array();
        if (array_key_exists('product_list', $response)) {
            foreach ($response['product_list'] as $productData) {
                $this->products[$productData['id']] = new \Model\Cart\Product\Entity($productData);
            }
        }

        $this->services = array();
        if (array_key_exists('service_list', $response)) {
            foreach ($response['service_list'] as $serviceData) {
                $service = new \Model\Cart\Service\Entity($serviceData);
                $productId = (array_key_exists('product_id', $serviceData)) ? (int)$serviceData['product_id'] : null;
                if ($productId && !empty($this->products[$productId])) {
                    $this->products[$productId]->addService($service);
                } else {
                    $this->services[$serviceData['id']] = $service;
                }
            }
        }

        $this->warranties = array();
        if(array_key_exists('warranty_list', $response)) {
            foreach($response['warranty_list'] as $warrantyData) {
                $warranty = new \Model\Cart\Service\Entity($warrantyData);
                $productId = (array_key_exists('product_id', $warrantyData)) ? (int)$warrantyData['product_id'] : null;
                if ($productId && !empty($this->products[$productId])) {
                    $this->products[$productId]->addWarranty($warranty);
                } else {
                    $this->services[$warrantyData['id']] = $warranty;
                }
            }
        }
    }

    public function getAnalyticsData() {
        $data = array();

        foreach ($this->getProductData() as $product) {
            $data[] = $product['id'];
        }

        return implode(',', $data);
    }
}