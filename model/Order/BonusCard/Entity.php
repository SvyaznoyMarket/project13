<?php

namespace Model\Order\BonusCard;

class Entity {
    const SVYAZNOY_ID = 1;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var string */
    private $image;
    /** @var string */
    private $mask;
    /** @var string */
    private $prefix;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('img', $data)) $this->setImage($data['img']);
        if (array_key_exists('mask', $data)) $this->setMask($data['mask']);
        if (array_key_exists('prefix', $data)) $this->setPrefix($data['prefix']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param string $mask
     */
    public function setMask($mask) {
        $this->mask = $mask;
    }

    /**
     * @return string
     */
    public function getMask() {
        return $this->mask;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->prefix;
    }
} 