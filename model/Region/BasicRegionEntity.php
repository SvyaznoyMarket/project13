<?php

namespace Model\Region;


class BasicRegionEntity {

    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = $data['id'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (array_key_exists('token', $data)) $this->token = $data['token'];
        if (array_key_exists('coord_long', $data)) $this->longitude = $data['coord_long'];
        if (array_key_exists('coord_lat', $data)) $this->latitude = $data['coord_lat'];
    }

}