<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Property {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $unit;
    /** @var string */
    public $hint;
    /** @var string */
    public $valueHint;
    /** @var bool */
    public $isMultiple;
    /** @var string */
    public $value;
    /** @var string */
    public $groupId;
    /** @var int */
    public $groupPosition;
    /** @var int */
    public $position;
    /** @var bool */
    public $isInList;
    /** @var string */
    public $shownValue;

    /**
     * @param array $data
     */
    public function import(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('unit', $data)) $this->unit = (string)$data['unit'];
        if (array_key_exists('hint', $data)) $this->hint = (string)$data['hint'];
        if (array_key_exists('value_hint', $data)) $this->valueHint = (string)$data['value_hint'];
        if (array_key_exists('is_multiple', $data)) $this->isMultiple = (bool)$data['is_multiple'];
        if (array_key_exists('value', $data)) $this->value = $data['value'];
        if (array_key_exists('group_id', $data)) $this->groupId = $data['group_id'] ? (string)$data['group_id'] : null;
        if (array_key_exists('group_position', $data)) $this->groupPosition = (int)$data['group_position'];
        if (array_key_exists('position', $data)) $this->position = (int)$data['position'];
        if (array_key_exists('is_view_list', $data)) $this->isInList = (bool)$data['is_view_list'];

        if ($this->isMultiple && isset($data['option'][0])) {
            foreach ($data['option'] as $optionItem) {
                if (!isset($optionItem['hint'])) continue;
                $this->valueHint .= $optionItem['hint'];
            }
        }

        if (null !== $this->value) {
            if (in_array($this->value, ['false', false], true)) {
                $this->shownValue = 'нет';
            } else if (in_array($this->value, ['true', true], true)) {
                $this->shownValue = 'да';
            } else {
                $this->shownValue = $this->value;
            }
        } else if (isset($data['option'][0])) {
            $value = [];
            foreach ($data['option'] as $optionItem) {
                if (in_array($optionItem['value'], ['false', false], true)) {
                    $value[] = 'нет';
                } else if (in_array($optionItem['value'], ['true', true], true)) {
                    $value[] = 'да';
                } else {
                    $value[] = $optionItem['value'];
                }
            }

            $this->shownValue = implode(', ', $value);
        } else {
            $this->shownValue = null;
        }
    }
}