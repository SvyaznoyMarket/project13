<?php

namespace Model\Brand;

class Entity {
    /** @var string */
    public $ui;
    /** @var int */
    public $id;
    /** @var string */
    public $token;
    /** @var string */
    public $name;
    /** @var string */
    public $image;

    public function __construct(array $data = []) {
        if (array_key_exists('ui', $data)) $this->ui = $data['ui'];
        if (array_key_exists('id', $data)) $this->id = $data['id'];
        if (array_key_exists('token', $data)) $this->token = $data['token'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (array_key_exists('media_image', $data)) $this->image = $data['media_image'];
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
}