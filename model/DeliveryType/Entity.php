<?php

namespace Model\DeliveryType;

class Entity {
    const TYPE_STANDART = 'standart';
    const TYPE_SELF = 'self';
    const TYPE_NOW = 'now';

    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $description;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
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