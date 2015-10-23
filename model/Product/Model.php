<?php

namespace Model\Product;

use Model\Product\Model\Property;

class Model {
    /** @var string */
    public $ui = '';
    /** @var Property\Entity|null */
    public $property;

    public function __construct($data = []) {
        if (isset($data['uid'])) $this->ui = (string)$data['uid'];
        if (!empty($data['property']) && !empty($data['items'])) $this->property = new Property\Entity($data);
    }
}