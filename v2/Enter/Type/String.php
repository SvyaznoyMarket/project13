<?php

namespace Enter\Type;

class String extends Basic {
    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value) {
        if (!is_scalar($value) && (null !== $value)) {
            throw new \InvalidArgumentException();
        }

        $this->value = (string)$value;
    }
}