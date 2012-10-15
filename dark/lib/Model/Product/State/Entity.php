<?php

namespace Model\Product\State;

class Entity {
    /** @var int */
    private $statusId;
    /** @var bool */
    private $isImage;
    /** @var bool */
    private $isPrice;
    /** @var bool */
    private $isShop;
    /** @var bool */
    private $isStore;
    /** @var bool */
    private $isSupplier;
    /** @var bool */
    private $isInList;
    /** @var bool */
    private $isInCard;
    /** @var bool */
    private $isBuyable;
    /** @var bool */
    private $isQuickOnly;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('status_id', $data)) $this->setStatusId($data['status_id']);
        if (array_key_exists('is_image', $data)) $this->setIsImage($data['is_image']);
        if (array_key_exists('is_price', $data)) $this->setIsPrice($data['is_price']);
        if (array_key_exists('is_shop', $data)) $this->setIsShop($data['is_shop']);
        if (array_key_exists('is_store', $data)) $this->setIsStore($data['is_store']);
        if (array_key_exists('is_supplier', $data)) $this->setIsSupplier($data['is_supplier']);
        if (array_key_exists('is_view_list', $data)) $this->setIsInList($data['is_view_list']);
        if (array_key_exists('is_view_card', $data)) $this->setIsInCard($data['is_view_card']);
        if (array_key_exists('is_buyable', $data)) $this->setIsBuyable($data['is_buyable']);
        if (array_key_exists('is_quick_only', $data)) $this->setIsQuickOnly($data['is_quick_only']);
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
     * @param bool $is_buyable
     */
    public function setIsQuickOnly($is_quick_only) {
        $this->isQuickOnly = (bool)$is_quick_only;
    }

    /**
     * @return bool
     */
    public function getIsQuickOnly() {
        return $this->isQuickOnly;
    }

    /**
     * @param bool $is_image
     */
    public function setIsImage($is_image) {
        $this->isImage = (bool)$is_image;
    }

    /**
     * @return bool
     */
    public function getIsImage() {
        return $this->isImage;
    }

    /**
     * @param bool $is_price
     */
    public function setIsPrice($is_price) {
        $this->isPrice = (bool)$is_price;
    }

    /**
     * @return bool
     */
    public function getIsPrice() {
        return $this->isPrice;
    }

    /**
     * @param bool $is_shop
     */
    public function setIsShop($is_shop) {
        $this->isShop = (bool)$is_shop;
    }

    /**
     * @return bool
     */
    public function getIsShop() {
        return $this->isShop;
    }

    /**
     * @param bool $is_store
     */
    public function setIsStore($is_store) {
        $this->isStore = (bool)$is_store;
    }

    /**
     * @return bool
     */
    public function getIsStore() {
        return $this->isStore;
    }

    /**
     * @param bool $is_supplier
     */
    public function setIsSupplier($is_supplier) {
        $this->isSupplier = (bool)$is_supplier;
    }

    /**
     * @return bool
     */
    public function getIsSupplier() {
        return $this->isSupplier;
    }

    /**
     * @param bool $is_view_card
     */
    public function setIsInCard($is_view_card) {
        $this->isInCard = (bool)$is_view_card;
    }

    /**
     * @return bool
     */
    public function getIsInCard() {
        return $this->isInCard;
    }

    /**
     * @param bool $is_view_list
     */
    public function setIsInList($is_view_list) {
        $this->isInList = (bool)$is_view_list;
    }

    /**
     * @return bool
     */
    public function getIsInList() {
        return $this->isInList;
    }

    /**
     * @param int $status_id
     */
    public function setStatusId($status_id) {
        $this->statusId = (int)$status_id;
    }

    /**
     * @return int
     */
    public function getStatusId() {
        return $this->statusId;
    }
}
