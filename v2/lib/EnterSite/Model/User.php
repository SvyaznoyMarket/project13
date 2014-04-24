<?php

namespace EnterSite\Model;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class User {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $firstName;
    /** @var string */
    public $lastName;
    /** @var string */
    public $middleName;
    /** @var string */
    public $regionId;
    /** @var Model\Region|null */
    public $region;

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('first_name', $data)) $this->firstName = (string)$data['first_name'];
        if (array_key_exists('last_name', $data)) $this->lastName = (string)$data['last_name'];
        if (array_key_exists('middle_name', $data)) $this->middleName = (string)$data['middle_name'];
        if (array_key_exists('geo_id', $data)) $this->regionId = (string)$data['geo_id'];

        if (isset($data['geo']['id'])) $this->region = new Model\Region($data['geo']);
    }
}