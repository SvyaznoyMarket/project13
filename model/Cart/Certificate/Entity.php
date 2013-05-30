<?php

namespace Model\Cart\Certificate;

class Entity {
    /** @var string */
    private $number;
    /** @var string */
    private $name;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = (string)$number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
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
