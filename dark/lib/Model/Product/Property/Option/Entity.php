<?php

namespace Model\Product\Property\Option;

/**
 * Опция у свойства товара
 */
class Entity {
    /* @var int */
    private $id;
    /* @var string */
    private $value;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('value', $data)) $this->setValue($data['value']);
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
    public function setValue($name) {
        $this->value = (string)$name;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    public function getHumanizedName() {
        if (in_array($this->value, array('false', false), true)) {
            return 'нет';
        }
        if (in_array($this->value, array('true', true), true)) {
            return 'да';
        }

        return $this->value;
    }
}