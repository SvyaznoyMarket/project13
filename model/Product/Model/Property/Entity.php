<?php

namespace Model\Product\Model\Property;

class Entity {

    /** @var int */
    private $id;
    /** @var int */
    private $position;
    /** @var bool */
    private $isImage;
    /** @var int */
    private $typeId;
    /** @var string */
    private $name;
    /** @var string */
    private $unit;
    /** @var bool */
    private $isMultiple;
    /** @var Option\Entity[] */
    private $option = [];

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
        if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
        if (array_key_exists('is_image', $data)) $this->setIsImage($data['is_image']);
        if (array_key_exists('position', $data)) $this->setPosition($data['position']);
        if (array_key_exists('option', $data) && is_array($data['option'])) foreach ($data['option'] as $option) {
            $this->addOption(new Option\Entity($option));
        }

    }

    /**
     * @param boolean $isImage
     */
    public function setIsImage($isImage) {
        $this->isImage = $isImage;
    }

    /**
     * @return boolean
     */
    public function getIsImage() {
        return $this->isImage;
    }

    /**
     * @param boolean $isMultiple
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = $isMultiple;
    }

    /**
     * @return boolean
     */
    public function getIsMultiple() {
        return $this->isMultiple;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param array $options
     */
    public function setOption(array $options) {
        $this->option = [];
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    /**
     * @param Option\Entity $option
     */
    public function addOption(Option\Entity $option) {
        $this->option[] = $option;
    }

    /**
     * @return array|Option\Entity[]
     */
    public function getOption() {
        return $this->option;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = $typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit) {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }
}
