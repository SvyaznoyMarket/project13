<?php

namespace Model\Subscribe\Channel;

class Entity {
    /* @var int */
    public $id;
    /* @var string */
    public $name;
    /** @var bool */
    public $isActive;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (int)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('is_active', $data)) $this->isActive = (bool)$data['is_active'];
    }
}