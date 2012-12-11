<?php

namespace Model\Subway;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var int */
    private $regionId;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('geo_id', $data)) $this->setRegionId($data['geo_id']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param int $regionId
     */
    public function setRegionId($regionId) {
        $this->regionId = (int)$regionId;
    }

    /**
     * @return int
     */
    public function getRegionId() {
        return $this->regionId;
    }
}
