<?php

namespace Enter\Type\Route\Product;

use Enter\Type\Collection;

class FilterCollection extends Collection {
    /**
     * @param string $offset
     * @param Filter $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value) {
        if (!$value instanceof Filter) {
            throw new \InvalidArgumentException();
        }
        if (!is_string($offset) || empty($offset)) {
            throw new \InvalidArgumentException();
        }

        $this->value[$offset] = $value;
    }
}