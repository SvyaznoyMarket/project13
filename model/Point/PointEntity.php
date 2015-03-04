<?php

namespace Model\Point;

use Model\Shop\Subway\Entity as Subway;
use Model\Region\BasicRegionEntity as Region;

class PointEntity {

    const TYPE_SHOP = 'shop';
    const TYPE_PICKPOINT = 'pickpoint';
    const TYPE_SVYAZNOY = 'svyaznoy';

    /** @var int */
    public $id;
    /** @var string */
    public $ui;
    /** @var string */
    public $name;
    /** @var string */
    public $address;
    /** @var Region */
    public $region;
    /** @var Subway */
    public $subway;
    /** @var string */
    public $type;

    function __construct(array $data = []) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['ui'])) $this->ui = $data['ui'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['address'])) $this->address = $data['address'];
        if (isset($data['geo'])) $this->region = new Region($data['geo']);
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['subway']) && is_array($data['subway'])) $this->subway = new Subway($data['subway']);
    }

    /** Магазин Enter?
     * @return bool
     */
    public function isEnterShop(){
        return $this->type == self::TYPE_SHOP;
    }

    /** Пункт самовывоза Pickpoint?
     * @return bool
     */
    public function isPickpoint(){
        return $this->type == self::TYPE_PICKPOINT;
    }

    /** Магазин Связного?
     * @return bool
     */
    public function isSvyaznoyShop(){
        return $this->type == self::TYPE_SVYAZNOY;
    }

}