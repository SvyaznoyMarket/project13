<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Shop {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $regionId;
    /** @var string */
    public $regime;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var string */
    public $address;
    /** @var Model\Region|null */
    public $region;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('geo_id', $data)) $this->regionId = (string)$data['geo_id'];
        if (array_key_exists('working_time', $data)) $this->regime = (string)$data['working_time'];
        if (array_key_exists('coord_long', $data)) $this->longitude = (float)$data['coord_long'];
        if (array_key_exists('coord_lat', $data)) $this->latitude = (float)$data['coord_lat'];
        if (array_key_exists('address', $data)) $this->address = (string)$data['address'];

        if (isset($data['geo']['id'])) $this->region = new Model\Region($data['geo']);
    }
}