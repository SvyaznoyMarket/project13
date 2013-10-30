<?php

namespace Enter\Type;

class Basic {
    protected $value;

    public function __construct($value = null) {
        if (null !== $value) {
            $this->setValue($value);
        }
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }
}