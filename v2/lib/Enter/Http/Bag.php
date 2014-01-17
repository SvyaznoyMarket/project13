<?php

namespace Enter\Http;

class Bag implements \ArrayAccess, \IteratorAggregate, \Countable {
    use BagTrait;
}