<?php

namespace Model\Product\Filter;

use Templating\Helper;

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
    public $groupUi;
    /** @var string */
    public $groupName;
    /** @var int */
    public $groupPosition;
    /** @var string */
    private $unit;
    /** @var bool */
    private $isMultiple;
    /** @var bool */
    private $isAlwaysShow;
    /** @var bool */
    private $isSlider;
    /** @var float */
    private $min;
    /** @var float */
    private $max;
    /**
     * @var float
     * @deprecated
     */
    private $minGlobal;
    /**
     * @var float
     * @deprecated
     */
    private $maxGlobal;
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
    /** @var bool */
    public $isOpenByDefault = false;
    /** @var string */
    public $defaultTitle = '';

    public function __construct(array $data = []) {
        if (array_key_exists('filter_id', $data)) $this->setId($data['filter_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (isset($data['group']['uid'])) $this->groupUi = $data['group']['uid'];
        if (isset($data['group']['name'])) $this->groupName = $data['group']['name'];
        if (isset($data['group']['position'])) $this->groupPosition = (int)$data['group']['position'];
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
        if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
        if (array_key_exists('always_show', $data)) $this->setIsAlwaysShow($data['always_show']);
        if (array_key_exists('is_slider', $data)) $this->setIsSlider($data['is_slider']);
        if (array_key_exists('min', $data)) $this->setMin($data['min']);
        if (array_key_exists('max', $data)) $this->setMax($data['max']);
        if (array_key_exists('options', $data) && (bool)$data['options']) {
            foreach ($data['options'] as $optionData) {
                $this->addOption(new Option\Entity($optionData));
            }
        }
        if (array_key_exists('step', $data)) $this->setStepType($data['step']);
        if (array_key_exists('min_global', $data)) $this->setMinGlobal($data['min_global']);
        if (array_key_exists('max_global', $data)) $this->setMaxGlobal($data['max_global']);
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
     * @param bool $isAlwaysShow
     */
    public function setIsAlwaysShow($isAlwaysShow) {
        $this->isAlwaysShow = (bool)$isAlwaysShow;
    }

    /**
     * @return bool
     */
    public function getIsAlwaysShow() {
        return $this->isAlwaysShow;
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
     * @return Option\Entity
     */
    public function deleteOption($expectedOption) {
        foreach ($this->option as $key => $option) {
            if ($option === $expectedOption) {
                unset($this->option[$key]);
            }
        }
    }

    /**
     * @return Option\Entity
     */
    public function deleteLastOption() {
        return array_pop($this->option);
    }

    /**
     * @return Option\Entity
     */
    public function deleteAllOptions() {
        $this->option = [];
    }

    /**
     * @param Option\Entity $option
     */
    public function unshiftOption(Option\Entity $option) {
        array_unshift($this->option, $option);
    }

    /**
     * @return Option\Entity[]
     */
    public function getOption() {
        return $this->option;
    }
    
    /**
     * @param Option\Entity[] $options
     * @return Option\Entity|null
     */
    public function getSelectedOption(\Model\Product\Filter $productFilter, array $options = []) {
        $selectedOption = null;

        if (!$options) {
            $options = $this->getOption();
        }
        
        $values = $productFilter->getValue($this);
        foreach ($options as $option) {
            if (in_array($option->getId(), $values)) {
                $selectedOption = $option;
                break;
            }
        }

        return $selectedOption;
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
        return 'price' === $this->getId();
    }

    public function isLabel() {
        return 'label' === $this->getId();
    }

    public function isShop() {
        return 'shop' === $this->getId();
    }

    public function isMetall() {
        return 'Металл' === $this->getName();
    }

    public function isInsertion() {
        return 'Вставка' === $this->getName();
    }

    /**
     * Возвращает диапазоны цен для нового фильтра
     * @return array
     */
    public function getPriceRanges() {
        $ranges = [];
        $min = $this->getMin();
        $max = $this->getMax();
        $num = $min;
        $previousNum = 0;
        while ($num < $max) {
            $num += ceil(($max - $min) / 5);
            $range = [];

            if ($previousNum) {
                $range['from'] = $previousNum + 1;
            }

            if ($num > 1000) {
                $num = ceil($num / 100) * 100;
            } else if ($num > 100) {
                $num = ceil($num / 10) * 10;
            }

            if ($num < $max) {
                $range['to'] = $num;
            }

            $previousNum = $num;
            $ranges[] = $range;
        }

        $helper = new Helper();
        foreach ($ranges as $key => $range) {
            $ranges[$key]['url'] = $helper->replacedUrl(['f-price-from' => isset($range['from']) ? $range['from'] : null, 'f-price-to' => isset($range['to']) ? $range['to'] : null, 'page' => null, 'ajax' => null]);
        }

        return $ranges;
    }

    /**
     * @return bool
     */
    public function isBrand() {
        return 'brand' === $this->getId();
    }

    /**
     * @param float $maxGlobal
     * @deprecated
     */
    public function setMaxGlobal($maxGlobal) {
        $this->maxGlobal = $maxGlobal;
    }

    /**
     * @return float
     * @deprecated
     */
    public function getMaxGlobal() {
        return $this->maxGlobal;
    }

    /**
     * @param float $minGlobal
     * @deprecated
     */
    public function setMinGlobal($minGlobal) {
        $this->minGlobal = $minGlobal;
    }

    /**
     * @return float
     * @deprecated
     */
    public function getMinGlobal() {
        return $this->minGlobal;
    }
}