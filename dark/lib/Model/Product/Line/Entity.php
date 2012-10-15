<?php

namespace Model\Product\Line;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var int */
    private $productCount;
    /** @var int */
    private $kitCount;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('product_count', $data)) $this->setProductCount($data['product_count']);
        if (array_key_exists('kit_count', $data)) $this->setKitCount($data['kit_count']);
    }

    public function setId($id) {
        $this->id = (int)$id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string)$name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param int $kitCount
     */
    public function setKitCount($kitCount) {
        $this->kitCount = (int)$kitCount;
    }

    /**
     * @return int
     */
    public function getKitCount() {
        return $this->kitCount;
    }

    /**
     * @param int $productCount
     */
    public function setProductCount($productCount) {
        $this->productCount = (int)$productCount;
    }

    /**
     * @return int
     */
    public function getProductCount() {
        return $this->productCount;
    }
}
