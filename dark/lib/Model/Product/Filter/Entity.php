<?php

namespace Model\Product\Filter;

class Entity {
    const TYPE_BOOLEAN = 1;
    const TYPE_DATE = 2;
    const TYPE_NUMBER = 3;
    const TYPE_STRING = 4;
    const TYPE_LIST = 5;
    const TYPE_SLIDER = 6;

    private $filterId;
    private $typeId;
    private $name;
    private $unit;
    private $isMultiple;
    private $isSlider;
    private $min;
    private $max;
    private $option = array();

    public function __construct(array $data = array()) {
        if (array_key_exists('filter_id', $data)) $this->setFilterId($data['filter_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
        if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
        if (array_key_exists('is_slider', $data)) $this->setIsSlider($data['is_slider']);
        if (array_key_exists('min', $data)) $this->setMin($data['min']);
        if (array_key_exists('max', $data)) $this->setMax($data['max']);
        if (array_key_exists('options', $data) && (bool)$data['options']) {
            foreach ($data['options'] as $optionData) {
                $this->addOption(new Option\Entity($optionData));
            }
        }
    }

    public function toArray() {
        return array(
            'filter_id'   => $this->filterId,
            'name'        => $this->name,
            'type_id'     => $this->typeId,
            'unit'        => $this->unit,
            'is_multiple' => $this->isMultiple,
            'is_slider'   => $this->isSlider,
            'min'         => $this->min,
            'max'         => $this->max,
            'options'     => $this->option,
        );
    }

    public function setFilterId($filterId) {
        $this->filterId = $filterId;
    }

    public function getFilterId() {
        return $this->filterId;
    }

    public function setIsMultiple($isMultiple) {
        $this->isMultiple = (bool)$isMultiple;
    }

    public function getIsMultiple() {
        return $this->isMultiple;
    }

    public function setIsSlider($isSlider) {
        $this->isSlider = (bool)$isSlider;
    }

    public function getIsSlider() {
        return $this->isSlider;
    }

    public function setMax($max) {
        $this->max = $max;
    }

    public function getMax() {
        return $this->max;
    }

    public function setMin($min) {
        $this->min = $min;
    }

    public function getMin() {
        return $this->min;
    }

    public function setName($name) {
        $this->name = (string)$name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param Option\Entity[] $options
     */
    public function setOption(array $options) {
        $this->option = array();
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
     * @return Option\Entity[]
     */
    public function getOption() {
        return $this->option;
    }

    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    public function getTypeId() {
        return $this->typeId;
    }

    public function setUnit($unit) {
        $this->unit = (string)$unit;
    }

    public function getUnit() {
        return $this->unit;
    }
}