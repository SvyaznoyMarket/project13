<?php

namespace Model\Product\Filter;

class Group {
    /** @var string */
    public $ui;
    /** @var string */
    public $name;
    /** @var int */
    public $position;
    /** @var Entity[] */
    public $properties = [];
    /** @var bool */
    public $hasSelectedProperties = false;

    public function hasInListProperties() {
        foreach ($this->properties as $property) {
            if ($property->getIsInList()) {
                return true;
            }
        }

        return false;
    }
}