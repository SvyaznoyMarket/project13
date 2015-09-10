<?php

namespace Model\Banner\Item;

class Entity {
    const TYPE_PRODUCT = 1;
    const TYPE_PRODUCT_CATEGORY = 3;

    /** @var int */
    private $typeId;
    /** @var int */
    private $productId;
    /** @var int */
    private $productCategoryId;

    public function __construct(array $data = []) {
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('id', $data)) {
            switch ($this->typeId) {
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