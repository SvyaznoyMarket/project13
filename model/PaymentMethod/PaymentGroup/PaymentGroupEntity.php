<?php


namespace Model\PaymentMethod\PaymentGroup;


class PaymentGroupEntity {

    const PAYMENT_ON_DELIVERY = 1;
    const PAYMENT_NOW = 2;

    public $id;
    public $name;
    public $description;

    public function __construct($arr) {

        if (isset($arr['id'])) $this->id = (int)$arr['id'];

        if (isset($arr['name'])) $this->name = (string)$arr['name'];

        if (isset($arr['description'])) $this->description = (string)$arr['description'];
    }

} 