<?php

namespace Model\Product\Type;

/**
 * Вид номенклатуры товара в 1С.
 */
class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
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
}
