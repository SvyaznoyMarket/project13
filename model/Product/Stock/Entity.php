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
    public function __construct(array $data = []) {
        if (array_key_exists('store_id', $data))            $this->setStoreId($data['store_id']);
        if (array_key_exists('shop_id', $data))             $this->setShopId($data['shop_id']);
        if (array_key_exists('is_supplier', $data))         $this->setIsSupplier($data['is_supplier']);
        if (array_key_exists('quantity', $data))            $this->setQuantity($data['quantity']);
        if (array_key_exists('quantity_reserve', $data))    $this->setQuantityReserve($data['quantity_reserve']);
        if (array_key_exists('quantity_entry', $data))      $this->setQuantityEntry($data['quantity_entry']);
        if (array_key_exists('quantity_showroom', $data))   $this->setQuantityShowroom($data['quantity_showroom']);
        if (array_key_exists('supplier_period', $data))     $this->setSupplierPeriod($data['supplier_period']);
        if (array_key_exists('supplier_date', $data))       $this->setSupplierDate($data['supplier_date']);
        if (array_key_exists('store_reserve_id', $data))    $this->setStoreReserveId($data['store_reserve_id']);
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
