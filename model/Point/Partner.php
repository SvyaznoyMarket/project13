<?php

namespace Model\Point;

class Partner {
    /** @var string */
    public $uid;
    /** @var string */
    public $slug;
    /** @var \Model\Inflections */
    public $names;

    function __construct($data = []) {
        $this->names = new \Model\Inflections();

        if (isset($data['uid'])) $this->uid = $data['uid'];
        if (isset($data['slug'])) $this->slug = $data['slug'];
        if (isset($data['name'])) $this->names->nominativus = $data['name'];
    }
}