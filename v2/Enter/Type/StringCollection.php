<?php

namespace Enter\Type;

use Enter\Type\Collection;

class StringCollection extends Collection {
    public function offsetSet($offset, $value) {
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException();
        }

        if (null === $offset) {
            $this->value[] = (string)$value;
        } else {
            $this->value[$offset] = (string)$value;
        }
    }
}