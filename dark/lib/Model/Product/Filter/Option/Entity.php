<?php

namespace Model\Product\Filter\Option;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var int */
    private $propertyId;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('property_id', $data)) $this->setPropertyId($data['property_id']);
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
     * @param int $propertyId
     */
    public function setPropertyId($propertyId) {
        $this->propertyId = (int)$propertyId;
    }

    /**
     * @return int
     */
    public function getPropertyId() {
        return $this->propertyId;
    }
}