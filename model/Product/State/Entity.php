<?php

namespace Model\Product\State;

class Entity {
    /** @var bool */
    private $isShop;
    /** @var bool */
    private $isStore;
    /** @var bool */
    private $isSupplier;
    /** @var bool */
    private $isBuyable;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('is_shop', $data)) $this->setIsShop($data['is_shop']);
        if (array_key_exists('is_store', $data)) $this->setIsStore($data['is_store']);
        if (array_key_exists('is_supplier', $data)) $this->setIsSupplier($data['is_supplier']);
        if (array_key_exists('is_buyable', $data)) $this->setIsBuyable($data['is_buyable']);
    }

    /**
     * @param bool $is_buyable
     */
    public function setIsBuyable($is_buyable) {
        $this->isBuyable = (bool)$is_buyable;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return $this->isBuyable;
    }

    /**
     * @param bool $isShop
     */
    public function setIsShop($isShop) {
        $this->isShop = (bool)$isShop;
    }

    /**
     * @return bool
     */
    public function getIsShop() {
        return $this->isShop;
    }

    /**
     * @param bool $isStore
     */
    public function setIsStore($isStore) {
        $this->isStore = (bool)$isStore;
    }

    /**
     * @return bool
     */
    public function getIsStore() {
        return $this->isStore;
    }

    /**
     * @param bool $isSupplier
     */
    public function setIsSupplier($isSupplier) {
        $this->isSupplier = (bool)$isSupplier;
    }

    /**
     * @return bool
     */
    public function getIsSupplier() {
        return $this->isSupplier;
    }
}
