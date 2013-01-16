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
     * @param $isQuickOnly
     */
    public function setIsQuickOnly($isQuickOnly) {
        $this->isQuickOnly = (bool)$isQuickOnly;
    }

    /**
     * @return bool
     */
    public function getIsQuickOnly() {
        return $this->isQuickOnly;
    }

    /**
     * @param bool $isImage
     */
    public function setIsImage($isImage) {
        $this->isImage = (bool)$isImage;
    }

    /**
     * @return bool
     */
    public function getIsImage() {
        return $this->isImage;
    }

    /**
     * @param bool $isPrice
     */
    public function setIsPrice($isPrice) {
        $this->isPrice = (bool)$isPrice;
    }

    /**
     * @return bool
     */
    public function getIsPrice() {
        return $this->isPrice;
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

    /**
     * @param bool $isViewCard
     */
    public function setIsInCard($isViewCard) {
        $this->isInCard = (bool)$isViewCard;
    }

    /**
     * @return bool
     */
    public function getIsInCard() {
        return $this->isInCard;
    }

    /**
     * @param bool $isInList
     */
    public function setIsInList($isInList) {
        $this->isInList = (bool)$isInList;
    }

    /**
     * @return bool
     */
    public function getIsInList() {
        return $this->isInList;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId) {
        $this->statusId = (int)$statusId;
    }

    /**
     * @return int
     */
    public function getStatusId() {
        return $this->statusId;
    }
}
