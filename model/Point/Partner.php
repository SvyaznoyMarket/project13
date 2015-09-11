<?php

namespace Model\Point;

class Partner {
    /** @var string */
    public $uid;
    /** @var string */
    public $slug;
    /** @var string */
    public $name;

    function __construct($data = []) {
        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['slug'])) $this->slug = $data['slug'];
        if (isset($data['name'])) $this->name = $data['name'];
    }
}