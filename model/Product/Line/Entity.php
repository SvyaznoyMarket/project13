<?php

namespace Model\Product\Line;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var int */
    private $productCount;
    /** @var int */
    private $kitCount;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
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
     * В этом методе каунтеры успользуют значения из product_count или kit_count
     *
     * @return int
     */
    public function getLineCount() {
        $ret = $this->getProductCount();
        if (!$ret) {
            $ret = $this->getKitCount();
        }
        return $ret;
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

    /**
     * @return int
     */
    public function getTotalCount() {
        return $this->productCount + $this->kitCount;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
}
