<?php

namespace Enter\Type\Query;

use Enter\Site\Curl\Query\Region\GetItemByHttpRequest;
use Enter\Site\Curl\Query\Region\GetItemById;
use Enter\Type\Basic;

class RegionObjectQuery extends Basic {
    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value) {
        if (false === (
            $value instanceof GetItemByHttpRequest
            || !$value instanceof GetItemById
        )) {
            throw new \InvalidArgumentException();
        }

        $this->value = $value;
    }
}