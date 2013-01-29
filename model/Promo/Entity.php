<?php

namespace Model\Promo;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var Image\Entity[] */
    private $image;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('image', $data) && is_array($data['image'])) $this->setImage($data['image']);
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
     * @param \Model\Promo\Image\Entity[] $image
     */
    public function setImage(array $image) {
        $this->image = $image;
    }

    /**
     * @return \Model\Promo\Image\Entity[]
     */
    public function getImage() {
        return $this->image;
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
}