<?php

namespace Enter\Http;

use JsonSerializable;

class Bag implements \ArrayAccess, \IteratorAggregate, \Countable, JsonSerializable {
    use BagTrait;

    public function jsonSerialize() {
        return $this->container;
    }
}