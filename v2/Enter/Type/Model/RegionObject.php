<?php

namespace Enter\Type\Model;

use Enter\Site\Model\Region as Object;
use Enter\Type\Basic;

/**
 * @property Object $value
 */
class RegionObject extends Basic {
    /**
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value) {
        if (!$value instanceof Object) {
            throw new \InvalidArgumentException();
        }

        $this->value = $value;
    }
}