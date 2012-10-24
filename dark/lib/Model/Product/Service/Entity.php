<?php

namespace Model\Product\Service;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $token;
    /** @var string */
    private $work;
    /** @var string */
    private $image;
    /** @var bool */
    private $isDelivered;
    /** @var bool */
    private $isInShop;
    /** @var int */
    private $price;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('work', $data)) $this->setWork($data['work']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('is_delivery', $data)) $this->setIsDelivered($data['is_delivery']);
        if (array_key_exists('is_in_shop', $data)) $this->setIsInShop($data['is_in_shop']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
     * @param boolean $isDelivered
     */
    public function setIsDelivered($isDelivered) {
        $this->isDelivered = (bool)$isDelivered;
    }

    /**
     * @return boolean
     */
    public function getIsDelivered() {
        return $this->isDelivered;
    }

    /**
     * @param boolean $isInShop
     */
    public function setIsInShop($isInShop) {
        $this->isInShop = (bool)$isInShop;
    }

    /**
     * @return boolean
     */
    public function getIsInShop() {
        return $this->isInShop;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = (int)$price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
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

    /**
     * @param string $work
     */
    public function setWork($work) {
        $this->work = (string)$work;
    }

    /**
     * @return string
     */
    public function getWork() {
        return $this->work;
    }

    /**
     * @return bool
     */
    public function isInSale() {
        return $this->getIsDelivered() && $this->getPrice() && $this->getPrice() > \App::config()->service['minPriceForDelivery'];
    }
}
