<?php

namespace Model\Tag;

class Entity {
    /** @var string */
    public $id = '';
    /** @var string */
    public $ui = '';
    /** @var string */
    public $name = '';
    /** @var string */
    public $token = '';

    public function __construct($data = []) {
        if (isset($data['core_id'])) $this->id = (string)$data['core_id'];
        if (isset($data['uid'])) $this->ui = (string)$data['uid'];
        if (isset($data['slug'])) $this->token = (string)$data['slug'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
    }
}