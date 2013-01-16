<?php

namespace Model\Banner\Item;

class Entity {
    const TYPE_PRODUCT = 1;
    const TYPE_SERVICE = 2;
    const TYPE_PRODUCT_CATEGORY = 3;

    /** @var int */
    private $typeId;
    /** @var int */
    private $productId;
    /** @var int */
    private $serviceId;
    /** @var int */
    private $productCategoryId;

    public function __construct(array $data = array()) {
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('id', $data)) {
            switch ($this->typeId) {
                case self::TYPE_PRODUCT:
                    $this->setProductId($data['id']);
                    break;
                case self::TYPE_SERVICE:
                    $this->setServiceId($data['id']);
                    break;
                case self::TYPE_PRODUCT_CATEGORY:
                    $this->setProductCategoryId($data['id']);
                    break;
            }
        }
    }

    /**
     * @param int $productCategoryId
     */
    public function setProductCategoryId($productCategoryId) {
        $this->productCategoryId = (int)$productCategoryId;
    }

    /**
     * @return int
     */
    public function getProductCategoryId() {
        return $this->productCategoryId;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId) {
        $this->productId = (int)$productId;
    }

    /**
     * @return int
     */
    public function getProductId() {
        return $this->productId;
    }

    /**
     * @param int $serviceId
     */
    public function setServiceId($serviceId) {
        $this->serviceId = (int)$serviceId;
    }

    /**
     * @return int
     */
    public function getServiceId() {
        return $this->serviceId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }
}