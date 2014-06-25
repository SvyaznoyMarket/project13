<?php

namespace Enter\Http;

class FileBag implements \ArrayAccess, \IteratorAggregate, \Countable {
    use BagTrait;

    public function offsetSet($offset, $value) {
        if (!$value instanceof File) {
            throw new \InvalidArgumentException();
        }

        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
}