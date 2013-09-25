<?php

namespace Model\Product\Filter\Option;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $link;
    /** @var int */
    private $quantity;


    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('quantity', $data)) $this->setQuantity($data['quantity']);
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
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity) {
        $this->quantity = (int)$quantity;
    }

    /**
     * @return quantity
     */
    public function getQuantity() {
        return $this->quantity;
    }

}