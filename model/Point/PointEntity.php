<?php

namespace Model\Point;

use Model\Region\BasicRegionEntity as Region;

/** Используется на последнем шаге ОЗ
 * Class PointEntity
 * @package Model\Point
 */
class PointEntity extends BasicPoint {

    const TYPE_SHOP = 'shop';
    const TYPE_PICKPOINT = 'pickpoint';
    const TYPE_SVYAZNOY = 'svyaznoy';
    const TYPE_HERMES = 'hermes';
    const TYPE_EUROSET = 'euroset';

    /** @var string */
    public $ui;
    /** @var Region */
    public $region;
    /** @var string */
    public $type;

    function __construct(array $data = []) {
        parent::__construct($data);
        if (isset($data['ui'])) $this->ui = $data['ui'];
        if (isset($data['geo'])) $this->region = new Region($data['geo']);
        if (isset($data['type'])) $this->type = $data['type'];
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

    /** Пункт выдачи Hermes?
     * @return bool
     */
    public function isHermesPoint(){
        return $this->type == self::TYPE_HERMES;
    }

    /** Магазин Евросеть?
     * @return bool
     */
    public function isEurosetPoint(){
        return $this->type == self::TYPE_EUROSET;
    }

    /**
     * @return string|null
     */
    public function getTypeName() {
        $name = null;

        if ($this->isPickpoint()) {
            $name = 'Постамат PickPoint';
        } else if ($this->isHermesPoint()) {
            $name = 'Постамат Hermes';
        } else if ($this->isEurosetPoint()) {
            $name = 'Евросеть';
        } else if ($this->isSvyaznoyShop()) {
            $name = 'Связной';
        } else if ($this->isEnterShop()) {
            $name = 'Магазин Enter';
        }

        return $name;
    }
}