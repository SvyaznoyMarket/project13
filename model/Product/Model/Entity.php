<?php

namespace Model\Product\Model;

class Entity {
    /** @var Property\Entity[] */
    protected $property = [];

    public function __construct(array $data = []) {
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

    /**
     * @return string
     */
    public function getVariations() {
        $list = [];
        foreach ($this->property as $property) {
            $list[] = mb_strtolower($property->getName());
        }

        return implode(', ', $list);
    }

    /**
     * @param string|int $id
     * @return Property\Entity|null
     */
    public function getPropertyById($id) {
        foreach ($this->property as $property) {
            if ($property->getId() == $id) {
                return $property;
            }
        }

        return null;
    }
}
