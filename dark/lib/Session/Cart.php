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
    private $productLimit = null;

    public function __construct() {
        $this->storage = \App::session();
        $session = $this->storage->all();

        $this->productLimit = \App::config()->cart['productLimit'];

        // если пользователь впервые, то заводим ему пустую корзину
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

    public function setProduct(\Model\Product\Entity $product, $quantity = 1) {
        $data = $this->storage->get($this->sessionName);
        if ($quantity > 0) {
            $data['productList'][$product->getId()] = $quantity;
            $this->storage->set($this->sessionName, $data);
        }
    }

    public function deleteProduct(\Model\Product\Entity $product) {
        // TODO: сделать
    }

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
     * TODO: переделать чтобы отдавала сущности \Model\Cart\Warranty\Entity
     * @param $productId
     * @return array
     */
    public function getWarrantyByProduct($productId) {
        $return = array();
        $data = $this->getData();

        foreach ($data['warrantyList'] as $warrantyId => $warranty) {
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
        $data = $this->getData();

        return isset($data['warrantyList'][$warrantyId][$productId]);
    }

    /**
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
     * @return array
     */
    public function getWarrantiesQuantity() {
        $data = $this->getData();
        $return = array();

        foreach($data['warrantyList'] as $warrantyId => $warrantiesByProduct) {
            foreach ($warrantiesByProduct as $productId => $warrantyQuantity) {
                $return[] =array(
                    'id'         => $warrantyId,
                    'quantity'   => $warrantyQuantity,
                    'product_id' => (int)$productId,
                );
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getProductData() {
        $data = $this->getData();
        $return = array();
        foreach ($data['productList'] as $productId => $productQuantity) {
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
        $data = $this->getData();
        $return = array();
        foreach ($data['serviceList'] as $serviceId => $serviceList) {
            foreach ($serviceList as $productId => $serviceQuantity) {
                $productId = (int)$productId;
                $item = array(
                    'id'       => $serviceId,
                    'quantity' => $serviceQuantity
                );

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
        $return = array();
        foreach($data['warrantyList'] as $warrantyId => $warrantiesByProduct) {
            foreach($warrantiesByProduct as $productId => $warrantyQuantity){
                $return[] =array(
                    'id'         => $warrantyId,
                    'quantity'   => $warrantyQuantity,
                    'product_id' => (int)$productId,
                );
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
                $warranty = new \Model\Cart\Warranty\Entity($warrantyData);
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
        $return = array();

        foreach ($this->getProductData() as $product) {
            $return[] = $product['id'];
        }

        return implode(',', $return);
    }

    private function checkProductLimit() {
        // если корзина не может вместить новый товар
        if (null != $this->productLimit && ($this->getProductsQuantity() >= $this->productLimit)) {
            $this->shiftProduct();
        }
    }
}