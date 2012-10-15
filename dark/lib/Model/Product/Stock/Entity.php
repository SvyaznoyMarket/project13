<?php

namespace Model\Product\Stock;

class Entity {
    /** @var int */
    private $storeId;
    /** @var int */
    private $shopId;
    /** @var bool */
    private $isSupplier;
    /** @var int */
    private $quantity;
    /** @var int */
    private $quantityReserve;
    /** @var int */
    private $quantityEntry;
    /** @var int */
    private $quantityShowroom;
    /** @var null */
    private $supplierPeriod;
    /** @var null */
    private $supplierDate;
    /** @var int|null */
    private $storeReserveId;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('store_id', $data)) $this->setStoreId($data['store_id']);
    }

    /**
     * @param boolean $isSupplier
     */
    public function setIsSupplier($isSupplier) {
        $this->isSupplier = $isSupplier;
    }

    /**
     * @return boolean
     */
    public function getIsSupplier() {
        return $this->isSupplier;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param int $quantityEntry
     */
    public function setQuantityEntry($quantityEntry) {
        $this->quantityEntry = $quantityEntry;
    }

    /**
     * @return int
     */
    public function getQuantityEntry() {
        return $this->quantityEntry;
    }

    /**
     * @param int $quantityReserve
     */
    public function setQuantityReserve($quantityReserve) {
        $this->quantityReserve = $quantityReserve;
    }

    /**
     * @return int
     */
    public function getQuantityReserve() {
        return $this->quantityReserve;
    }

    /**
     * @param int $quantityShowroom
     */
    public function setQuantityShowroom($quantityShowroom) {
        $this->quantityShowroom = $quantityShowroom;
    }

    /**
     * @return int
     */
    public function getQuantityShowroom() {
        return $this->quantityShowroom;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId) {
        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getShopId() {
        return $this->shopId;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId) {
        $this->storeId = $storeId;
    }

    /**
     * @return int
     */
    public function getStoreId() {
        return $this->storeId;
    }

    /**
     * @param int|null $storeReserveId
     */
    public function setStoreReserveId($storeReserveId) {
        $this->storeReserveId = $storeReserveId;
    }

    /**
     * @return int|null
     */
    public function getStoreReserveId() {
        return $this->storeReserveId;
    }

    /**
     * @param null $supplierDate
     */
    public function setSupplierDate($supplierDate) {
        $this->supplierDate = $supplierDate;
    }

    /**
     * @return null
     */
    public function getSupplierDate() {
        return $this->supplierDate;
    }

    /**
     * @param null $supplierPeriod
     */
    public function setSupplierPeriod($supplierPeriod) {
        $this->supplierPeriod = $supplierPeriod;
    }

    /**
     * @return null
     */
    public function getSupplierPeriod() {
        return $this->supplierPeriod;
    }
}
