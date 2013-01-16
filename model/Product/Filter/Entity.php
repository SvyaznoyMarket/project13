<?php

namespace Model\Product\Filter;

class Entity {
    const TYPE_BOOLEAN = 1;
    const TYPE_DATE = 2;
    const TYPE_NUMBER = 3;
    const TYPE_STRING = 4;
    const TYPE_LIST = 5;
    const TYPE_SLIDER = 6;

    /** @var string */
    private $id;
    /** @var int */
    private $typeId;
    /** @var string */
    private $name;
    /** @var string */
    private $unit;
    /** @var bool */
    private $isMultiple;
    /** @var bool */
    private $isSlider;
    /** @var int */
    private $min;
    /** @var int */
    private $max;
    /** @var Option\Entity[] */
    private $option = array();
    /**
     * Флаг: отображать фильтр в списке?
     *
     * @var bool
     */
    private $isInList = true;

    public function __construct(array $data = array()) {
        if (array_key_exists('filter_id', $data)) $this->setId($data['filter_id']);
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
            'filter_id'   => $this->id,
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

    /**
     * Осторожно: строковый тип!
     *
     * @param string $id
     */
    public function setId($id) {
        $this->id = (string)$id;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param bool $isMultiple
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = (bool)$isMultiple;
    }

    /**
     * @return bool
     */
    public function getIsMultiple() {
        return $this->isMultiple;
    }

    /**
     * @param bool $isSlider
     */
    public function setIsSlider($isSlider) {
        $this->isSlider = (bool)$isSlider;
    }

    /**
     * @return bool
     */
    public function getIsSlider() {
        return $this->isSlider;
    }

    /**
     * @param int $max
     */
    public function setMax($max) {
        $this->max = (int)$max;
    }

    /**
     * @return int
     */
    public function getMax() {
        return $this->max;
    }

    /**
     * @param int $min
     */
    public function setMin($min) {
        $this->min = (int)$min;
    }

    /**
     * @return int
     */
    public function getMin() {
        return $this->min;
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

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
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
        $this->unit = (string)$unit;
    }

    /**
     * @return string
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @param bool $isInList
     */
    public function setIsInList($isInList) {
        $this->isInList = (bool)$isInList;
    }

    /**
     * @return bool
     */
    public function getIsInList() {
        return $this->isInList;
    }
}