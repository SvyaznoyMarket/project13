<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;

class Region {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $parentId;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var float */
    public $latitude;
    /** @var float */
    public $longitude;
    /** @var bool */
    public $transportCompanyAvailable;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('parent_id', $data)) $this->parentId = (string)$data['parent_id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('coord_long', $data)) $this->longitude = (float)$data['coord_long'];
        if (array_key_exists('coord_lat', $data)) $this->latitude = (float)$data['coord_lat'];
        if (array_key_exists('tk_available', $data)) $this->transportCompanyAvailable = (bool)$data['tk_available'];
    }
}