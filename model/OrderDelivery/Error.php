<?php


namespace Model\OrderDelivery;

class Error {

    const CODE_MAX_QUANTITY = 708;

    /** @var int */
    public $code;
    /** @var string */
    public $message;
    /** @var array */
    public $details;

    public function __construct($arr, \Model\OrderDelivery\Entity &$order) {
        if (isset($arr['code'])) $this->code = (int)$arr['code'];
        if (isset($arr['message'])) $this->message = (string)$arr['message'];
        if (isset($arr['details'])) $this->details = $arr['details'];

        if (isset($arr['details']['block_name']) && isset($order->orders[$arr['details']['block_name']])) {
            $order->orders[$arr['details']['block_name']]->errors[] = &$this;
        }
    }

    public function isMaxQuantityError() {
        return $this->code == self::CODE_MAX_QUANTITY;
    }

}