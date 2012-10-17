<?php

namespace Session;

class Cart {

    /** @var string */
    private $sessionName = 'userCart';

    private $storage;

    public function __construct() {
        $this->storage = \App::session();
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
        return count($session[$this->sessionName]['productList']);
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