<?php

namespace Model\Point;

use Model\Shop\Subway\Entity as Subway;
use \Model\OrderDelivery\Entity\Subway as DeliverySubway;

class BasicPoint {

    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $address;
    /** @var DeliverySubway|Subway */
    public $subway;

    function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['address'])) $this->address = $data['address'];

        if (isset($data['subway'])) {
            if (is_array($data['subway'])) $this->subway = new Subway($data['subway']);
            if ($data['subway'] instanceof DeliverySubway) $this->subway = $data['subway'];
        }
    }

}