<?php


namespace Model\Order;

class StatusEntity {
    const ID_READY = 20;
    const ID_CANCEL = 100;

    /** @var string */
    public $id;
    /** @var string */
    public $name;

    function __construct($data) {
        if (isset($data['id'])) $this->id = (string)$data['id'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
    }
}