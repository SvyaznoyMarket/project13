<?php

namespace Model\Product\Property\Group;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var int */
    private $position;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('position', $data)) $this->setPosition($data['position']);
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

    public function setPosition($position) {
        $this->position = (int)$position;
    }

    public function getPosition() {
        return $this->position;
    }
}
