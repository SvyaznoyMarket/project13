<?php

namespace Model\Cart\Certificate;

class Entity {
    /** @var string */
    private $number;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
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
}
