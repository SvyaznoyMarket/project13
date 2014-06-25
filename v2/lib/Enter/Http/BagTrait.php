<?php

namespace Enter\Http;

trait BagTrait {
    private $container = [];

    public function __construct(array $container = []) {
        $this->container = $container;
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function getIterator() {
        return new \ArrayIterator($this->container);
    }

    public function count() {
        return count($this->container);
    }

    public function all() {
        return $this->container;
    }
}