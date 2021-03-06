<?php

namespace Model\Product\Property;
/**
 * Свойство товара
 */
class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $typeId;
    /** @var string */
    private $name;
    /** @var string */
    private $unit;
    /** @var string */
    private $hint;
    /** @var string */
    private $valueHint;
    /** @var bool */
    private $isMultiple;
    /** @var int */
    private $optionId;
    /** @var string */
    private $value;
    /** @var int */
    private $groupId;
    /** @var int */
    private $groupPosition;
    /** @var int */
    private $position = 1000;
    /** @var bool */
    private $isInList;
    /** @var Option\Entity[] */
    private $option = [];

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('unit', $data)) $this->setUnit($data['unit']);
        if (array_key_exists('hint', $data)) $this->setHint($data['hint']);
        if (array_key_exists('value_hint', $data)) $this->setValueHint($data['value_hint']);
        if (array_key_exists('is_multiple', $data)) $this->setIsMultiple($data['is_multiple']);
        if (array_key_exists('option_id', $data)) $this->setOptionId($data['option_id']);
        if (array_key_exists('value', $data)) $this->setValue($data['value']);
        if (array_key_exists('group_uid', $data)) {
            $this->setGroupId($data['group_uid']);
        } else if (array_key_exists('group_id', $data)) {
            $this->setGroupId($data['group_id']); // SITE-5290
        }
        if (array_key_exists('position_in_group', $data)) {
            $this->setGroupPosition($data['position_in_group']);
        } else if (array_key_exists('group_position', $data)) {
            $this->setGroupPosition($data['group_position']);
        }
        if (array_key_exists('position_in_list', $data)) {
            $this->setPosition($data['position_in_list']);
        } else  if (array_key_exists('position', $data)) {
            $this->setPosition($data['position']);
        }
        if (array_key_exists('is_view_list', $data)) $this->setIsInList($data['is_view_list']);
        if (isset($data['option'][0])) {
            $this->setOption(array_map(function($data) {
                return new Option\Entity($data);
            }, $data['option']));
        } else if (isset($data['options'][0])) {
            array_walk($data['options'], function($data) {
                if (isset($data['value'])) {
                    $this->addOption(new \Model\Product\Property\Option\Entity($data));
                }
            });
        }
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * @param int $groupPosition
     */
    public function setGroupPosition($groupPosition) {
        $this->groupPosition = (int)$groupPosition;
    }

    /**
     * @return int
     */
    public function getGroupPosition() {
        return $this->groupPosition;
    }

    /**
     * @param $hint
     */
    public function setHint($hint) {
        $this->hint = (string)$hint;
    }

    /**
     * @return mixed
     */
    public function getHint() {
        return $this->hint;
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
     * @param boolean $isMultiple
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = (boolean)$isMultiple;
    }

    /**
     * @return boolean
     */
    public function getIsMultiple() {
        return $this->isMultiple;
    }

    /**
     * @param boolean $isInList
     */
    public function setIsInList($isInList) {
        $this->isInList = (bool)$isInList;
    }

    /**
     * @return boolean
     */
    public function getIsInList() {
        return $this->isInList;
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
     * @param int $optionId
     */
    public function setOptionId($optionId) {
        $this->optionId = (int)$optionId;
    }

    /**
     * @return int
     */
    public function getOptionId() {
        return $this->optionId;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = (int)$position;
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

    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /** Возвращает значение первой опции
     * @return null|string
     */
    public function getOptionValue() {
        return isset($this->option[0]) ? $this->option[0]->getValue() : null;
    }

    public function getStringValue() {
        $value = implode(
            ', ',
            array_map(
                function(\Model\Product\Property\Option\Entity $option) {
                    return $option->getValue();
                },
                $this->getOption()
            )
        );

        if ($value && $this->unit) {
            $value .= ' ' . $this->unit;
        }

        return $value;
    }

    /**
     * @param string $valueHint
     */
    public function setValueHint($valueHint)
    {
        $this->valueHint = (string)$valueHint;
    }

    /**
     * @return string
     */
    public function getValueHint()
    {
        $hint = null;
        foreach ($this->getOption() as $option) {
            $hint .= $option->getHint();
        }

        return $hint;
    }
}
