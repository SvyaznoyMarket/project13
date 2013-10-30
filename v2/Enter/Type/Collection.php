<?php

namespace Enter\Type;

class Collection extends Basic implements \ArrayAccess, \IteratorAggregate, \Countable {
    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    final public function setValue($value) {
        if (!is_array($value)) {
            throw new \InvalidArgumentException();
        }

        foreach ($this->value as $k => $v) {
            $this->value[$k] = $v;
        }
    }

    public function offsetExists($offset) {
        return isset($this->value[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->value[$offset]) ? $this->value[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (null === $offset) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->value[$offset]);
    }

    public function getIterator() {
        return new \ArrayIterator($this->value);
    }

    public function count() {
        return count($this->value);
    }
}