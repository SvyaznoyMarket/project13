<?php

namespace Model\Tag;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $token;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
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

    public function setToken($token) {
        $this->token = (string)$token;
    }

    public function getToken() {
        return $this->token;
    }
}