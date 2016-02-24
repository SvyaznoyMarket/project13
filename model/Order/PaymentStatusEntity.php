<?php


namespace Model\Order;

class PaymentStatusEntity {
    /** @var string */
    public $id;
    /** @var string */
    public $name;

    /**
     * PaymentStatusEntity constructor.
     * @param $data
     */
    public function __construct($data) {
        if (isset($data['id'])) $this->id = (string)$data['id'];
        if (isset($data['name'])) $this->name = (string)$data['name'];
    }
}