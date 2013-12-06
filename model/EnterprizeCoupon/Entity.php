<?php

namespace Model\EnterprizeCoupon;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var int */
    private $price;
    /** @var bool */
    private $isCurrency;
    /** @var string */
    private $backgroundImage;

    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('image', $data)) $this->setImage($data['image']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('isCurrency', $data)) $this->setIsCurrency($data['isCurrency']);
        if (array_key_exists('backgroundImage', $data)) $this->setBackgroundImage($data['backgroundImage']);
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
     * @param boolean $isCurrency
     */
    public function setIsCurrency($isCurrency) {
        $this->isCurrency = (bool)$isCurrency;
    }

    /**
     * @return boolean
     */
    public function getIsCurrency() {
        return $this->isCurrency;
    }

    /**
     * @param string $backgroundImage
     */
    public function setBackgroundImage($backgroundImage) {
        $this->backgroundImage = (string)$backgroundImage;
    }

    /**
     * @return string
     */
    public function getBackgroundImage() {
        return $this->backgroundImage;
    }
}