<?php

namespace Model\Point;

class Town {
    /** @var \Model\Inflections */
    public $names;

    function __construct($data = []) {
        $this->names = new \Model\Inflections();
        if (isset($data['name'])) $this->names->nominativus = $data['name'];
    }
}