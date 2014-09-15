<?php

namespace Model\Brand;

class Entity {
    /** @var string */
    private $ui;
    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var string */
    private $image;
    /** @var bool */
    private $isInFilter;

    public function __construct(array $data = []) {
        if (array_key_exists('ui', $data)) $this->setUi($data['ui']);
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('is_view_filter', $data)) $this->setIsInFilter($data['is_view_filter']);
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = $ui;
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
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
     * @param boolean $isInFilter
     */
    public function setIsInFilter($isInFilter) {
        $this->isInFilter = (bool)$isInFilter;
    }

    /**
     * @return boolean
     */
    public function getIsInFilter() {
        return $this->isInFilter;
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
}