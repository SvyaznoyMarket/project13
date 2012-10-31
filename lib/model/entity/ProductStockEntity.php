<?php

class ProductStockEntity
{
    /** @var int */
    private $store_id;
    /** @var int */
    private $shop_id;
    /** @var bool */
    private $is_supplier;
    /** @var int */
    private $quantity;
    /** @var int */
    private $quantity_reserve;
    /** @var int */
    private $quantity_entry;
    /** @var int */
    private $quantity_showroom;
    private $supplier_period;
    private $supplier_date;
    /** @var int */
    private $store_reserve_id;

    public function __construct($data)
    {
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
     * @param boolean $is_supplier
     */
    public function setIsSupplier($is_supplier)
    {
        $this->is_supplier = (bool)$is_supplier;
    }

    /**
     * @return boolean
     */
    public function getIsSupplier()
    {
        return $this->is_supplier;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity_entry
     */
    public function setQuantityEntry($quantity_entry)
    {
        $this->quantity_entry = (int)$quantity_entry;
    }

    /**
     * @return int
     */
    public function getQuantityEntry()
    {
        return $this->quantity_entry;
    }

    /**
     * @param int $quantity_reserve
     */
    public function setQuantityReserve($quantity_reserve)
    {
        $this->quantity_reserve = (int)$quantity_reserve;
    }

    /**
     * @return int
     */
    public function getQuantityReserve()
    {
        return $this->quantity_reserve;
    }

    /**
     * @param int $quantity_showroom
     */
    public function setQuantityShowroom($quantity_showroom)
    {
        $this->quantity_showroom = (int)$quantity_showroom;
    }

    /**
     * @return int
     */
    public function getQuantityShowroom()
    {
        return $this->quantity_showroom;
    }

    /**
     * @param int $shop_id
     */
    public function setShopId($shop_id)
    {
        $this->shop_id = (int)$shop_id;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    /**
     * @param int $store_id
     */
    public function setStoreId($store_id)
    {
        $this->store_id = (int)$store_id;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * @param int $store_reserve_id
     */
    public function setStoreReserveId($store_reserve_id)
    {
        $this->store_reserve_id = (int)$store_reserve_id;
    }

    /**
     * @return int
     */
    public function getStoreReserveId()
    {
        return $this->store_reserve_id;
    }

    public function setSupplierDate($supplier_date)
    {
        $this->supplier_date = $supplier_date;
    }

    public function getSupplierDate()
    {
        return $this->supplier_date;
    }

    public function setSupplierPeriod($supplier_period)
    {
        $this->supplier_period = $supplier_period;
    }

    public function getSupplierPeriod()
    {
        return $this->supplier_period;
    }
}
