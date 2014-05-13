<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Shop {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $token;
    /** @var string */
    public $name;
    /** @var string */
    public $regionId;
    /** @var string */
    public $regime;
    /** @var string */
    public $phone;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var string */
    public $address;
    /** @var string */
    public $description;
    /** @var Model\Region|null */
    public $region;
    /** @var Model\Shop\Photo[] */
    public $photo = [];
    /** @var string */
    public $walkWay;
    /** @var string */
    public $carWay;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('geo_id', $data)) $this->regionId = (string)$data['geo_id'];
        if (array_key_exists('working_time', $data)) $this->regime = (string)$data['working_time'];
        if (array_key_exists('coord_long', $data)) $this->longitude = (float)$data['coord_long'];
        if (array_key_exists('coord_lat', $data)) $this->latitude = (float)$data['coord_lat'];
        if (array_key_exists('address', $data)) $this->address = (string)$data['address'];
        if (array_key_exists('phone', $data)) $this->phone = (string)$data['phone'];
        if (array_key_exists('description', $data)) $this->description = (string)$data['description'];
        if (array_key_exists('way_walk', $data)) $this->walkWay = (string)$data['way_walk'];
        if (array_key_exists('way_auto', $data)) $this->carWay = (string)$data['way_auto'];

        if (isset($data['geo']['id'])) {
            $this->region = new Model\Region($data['geo']);
            $this->regionId = $this->region->id; // FIXME: костыль для ядра: иногда не отдает geo_id
        };

        if (isset($data['images'][0])) {
            foreach ($data['images'] as $photoItem) {
                $this->photo[] = new Model\Shop\Photo($photoItem);
            }
        }
    }
}