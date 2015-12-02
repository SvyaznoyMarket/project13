<?php
namespace Model;

class Discount {
    /** @var string */
    public $action;
    /** @var float|null */
    public $sum;
    /** @var string */
    public $value;
    /** @var string */
    public $unit;

    /**
     * @param mixed $data
     */
    public function __construct($data = []) {
        if (isset($data['code'])) $this->action = (string)$data['code'];
        if (isset($data['sum'])) $this->sum = (float)$data['sum'];
        if (isset($data['value'])) $this->value = (string)$data['value'];
        if (isset($data['unit'])) $this->unit = (string)$data['unit'];
    }
}