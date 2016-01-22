<?php

namespace Model\User\Address;

class Entity {
    /** @var string */
    public $id;
    /** @var string */
    public $kladrId;
    /** @var string */
    public $regionId;
    /** @var \Model\Region\Entity|null */
    public $region;
    /** @var string */
    public $zipCode;
    /** @var string */
    public $name; // адрес
    /** @var string */
    public $street;
    /** @var string */
    public $streetType;
    /** @var string */
    public $building;
    /** @var string */
    public $apartment;
    /** @var string */
    public $description;
    /** @var int */
    public $priority;
    /** @var bool */
    public $lastSelection;

    /**
     * @param mixed $data
     */
    public function __construct($data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('kladr_id', $data)) $this->kladrId = (string)$data['kladr_id'];
        if (array_key_exists('geo_id', $data)) $this->regionId = (string)$data['geo_id'];
        if (array_key_exists('zip_code', $data)) $this->zipCode = (string)$data['zip_code'];
        if (array_key_exists('address', $data)) $this->address = (string)$data['address'];
        if (array_key_exists('street', $data)) $this->street = (string)$data['street'];
        if (array_key_exists('street_type', $data)) $this->streetType = (string)$data['street_type'];
        if (array_key_exists('building', $data)) $this->building = (string)$data['building'];
        if (array_key_exists('apartment', $data)) $this->apartment = (string)$data['apartment'];
        if (array_key_exists('description', $data)) $this->description = (string)$data['description'];
        if (array_key_exists('priority', $data)) $this->priority = (int)$data['priority'];
        if (array_key_exists('last_selection', $data)) $this->lastSelection = (bool)$data['last_selection'];
    }
}
