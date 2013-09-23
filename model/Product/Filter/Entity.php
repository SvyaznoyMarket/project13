<?php

namespace Model\Product\Filter;

class Entity {
    const TYPE_BOOLEAN = 1;
    const TYPE_DATE = 2;
    const TYPE_NUMBER = 3;
    const TYPE_STRING = 4;
    const TYPE_LIST = 5;
    const TYPE_SLIDER = 6;
    const TYPE_STEP_INTEGER = 'integer';
    const TYPE_STEP_FLOAT = 'fractional';

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
    /** @var float */
    private $min;
    /** @var float */
    private $max;
    /** @var Option\Entity[] */
    private $option = [];
    /**
     * Флаг: отображать фильтр в списке?
     *
     * @var bool
     */
    private $isInList = true;

    /** @var  string */
    private $stepType;

    public function __construct(array $data = []) {
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
        if (array_key_exists('step', $data)) $this->setStepType($data['step']);
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
        // TODO: осторожно, костыль
        return (self::TYPE_LIST == $this->typeId) && !in_array($this->id, ['shop']);
        //return $this->isMultiple;
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
     * @param float $max
     */
    public function setMax($max) {
        $this->max = (float)$max;
    }

    /**
     * @return float
     */
    public function getMax() {
        return $this->max;
    }

    /**
     * @param float $min
     */
    public function setMin($min) {
        $this->min = (float)$min;
    }

    /**
     * @return float
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

    /**
     * @param $stepType
     */
    public function setStepType($stepType)
    {
        $this->stepType = $stepType;
    }

    /**
     * @return string
     */
    public function getStepType()
    {
        return $this->stepType;
    }

    public function getStepByFilter() {
        switch ($this->getStepType()) {
            case self::TYPE_STEP_INTEGER : {
                return ($this->getId() == 'price') ? 100 : 1;
            }
            case self::TYPE_STEP_FLOAT : {
                return 0.1;
            }
            default: return false;
        }
    }

    /**
     * @return bool
     */
    public function isPrice() {
        return 'price' == $this->getId();
    }

    /**
     * @return bool
     */
    public function isBrand() {
        return 'brand' == $this->getId();
    }
}