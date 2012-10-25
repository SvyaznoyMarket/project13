<?php

namespace Session;

class Cart {

    /** @var string */
    private $sessionName = 'userCart';
    /** @var \Http\Session */
    private $storage;

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
     * @return array
     */
    public function getServicesByProduct($productId) {
        $return = array();
        $session = $this->storage->all();

        foreach ($session[$this->sessionName]['serviceList'] as $serviceId => $service) {
            if (array_key_exists($productId, $service)) {
                $return[] = $serviceId;
            }
        }

        return array_unique($return);
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

}