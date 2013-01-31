<?php

namespace Model\Cart\Product;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $quantity;
    /** @var int */
    private $price;
    /** @var bool */
    private $isBuyable = true;
    /** @var \Model\Cart\Service\Entity[] */
    private $service = [];
    /** @var \Model\Cart\Warranty\Entity[] */
    private $warranty = [];

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('error', $data)){
            // TODO: подумать - а надо ли это
            $this->setQuantity(0);

            $this->setIsBuyable(false);
        }
    }

    /**
     * @param $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = (int)$price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = (int)$quantity;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getTotalPrice() {
        return $this->quantity * $this->getPrice();
    }

    /**
     * @param bool $isBuyable
     */
    public function setIsBuyable($isBuyable) {
        $this->isBuyable = (bool)$isBuyable;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return $this->isBuyable;
    }

    /**
     * @param \Model\Cart\Service\Entity[] $services
     */
    public function setService(array $services) {
        $this->service = [];
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    /**\
     * @param \Model\Cart\Service\Entity $service
     */
    public function addService(\Model\Cart\Service\Entity $service) {
        $this->service[$service->getId()] = $service;
    }

    /**
     * @return array|\Model\Cart\Service\Entity[]
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param int $serviceId
     * @return \Model\Cart\Service\Entity|null
     */
    public function getServiceById($serviceId) {
        return isset($this->service[$serviceId]) ? $this->service[$serviceId] : null;
    }

    public function hasService($serviceId) {
        return array_key_exists($serviceId, $this->service);
    }

    /**
     * @param \Model\Cart\Warranty\Entity[] $warranties
     */
    public function setWarranty($warranties) {
        $this->warranty = [];
        foreach ($warranties as $warranty) {
            $this->addWarranty($warranty);
        }
    }

    /**
     * @param \Model\Cart\Warranty\Entity $warranty
     */
    public function addWarranty(\Model\Cart\Warranty\Entity $warranty) {
        $this->warranty[$warranty->getId()] = $warranty;
    }

    /**
     * @return \Model\Cart\Warranty\Entity[]
     */
    public function getWarranty() {
        return $this->warranty;
    }

    /**
     * @param int $warrantyId
     * @return \Model\Cart\Warranty\Entity|null
     */
    public function getWarrantyById($warrantyId) {
        return isset($this->warranty[$warrantyId]) ? $this->warranty[$warrantyId] : null;
    }

    /**
     * @param int $warrantyId
     * @return bool
     */
    public function hasWarranty($warrantyId) {
        return array_key_exists($warrantyId, $this->warranty);
    }
}
