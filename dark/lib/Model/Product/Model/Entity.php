<?php

namespace Model\Product\Model;

class Entity {
    /** @var Property\Entity[] */
    protected $property = array();

    public function __construct(array $data = array()) {
        if (array_key_exists('property', $data) && is_array($data['property'])) $this->setProperty(array_map(function($data) {
            return new Property\Entity($data);
        }, $data['property']));
    }

    /**
     * @param Property\Entity $property
     */
    public function setProperty($property) {
        $this->property = $property;
    }

    /**
     * @return array|Property\Entity[]
     */
    public function getProperty() {
        return $this->property;
    }
}
