<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;
use EnterSite\Model;

class Filter {
    use ImportArrayConstructorTrait;

    const TYPE_BOOLEAN = 1;
    const TYPE_DATE = 2;
    const TYPE_NUMBER = 3;
    const TYPE_STRING = 4;
    const TYPE_LIST = 5;
    const TYPE_SLIDER = 6;
    const TYPE_STEP_INTEGER = 'integer';
    const TYPE_STEP_FLOAT = 'fractional';

    /** @var string */
    public $token;
    /** @var string */
    public $name;
    /** @var int */
    public $typeId;
    /** @var bool */
    public $isMultiple;
    /** @var string */
    public $stepType;
    /** @var int|float */
    public $step;
    /** @var int */
    public $min;
    /** @var int */
    public $max;
    /** @var int */
    public $globalMin;
    /** @var int */
    public $globalMax;
    /** @var string|null */
    public $unit;
    /** @var Model\Product\Filter\Option[] */
    public $option = [];

    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('filter_id', $data)) $this->token = (string)$data['filter_id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('type_id', $data)) $this->typeId = (int)$data['type_id'];
        //if (array_key_exists('is_multiple', $data)) $this->isMultiple = (bool)$data['is_multiple'];
        // FIXME: костыль для ядра
        $this->isMultiple = in_array($this->typeId, [self::TYPE_LIST, self::TYPE_BOOLEAN]) && !in_array($this->token, ['shop', 'category']);
        if (array_key_exists('step', $data)) $this->stepType = (string)$data['step'];
        if (array_key_exists('min', $data)) $this->min = (int)$data['min'];
        if (array_key_exists('max', $data)) $this->max = (int)$data['max'];
        if (array_key_exists('min_global', $data)) $this->globalMin = (int)$data['min_global'];
        if (array_key_exists('max_global', $data)) $this->globalMax = (int)$data['max_global'];
        if (array_key_exists('unit', $data)) $this->unit = $data['unit'] ? (string)$data['unit'] : null;

        if ('price' == $this->token) {
            $this->step = 100;
        } else if ($this->stepType === self::TYPE_STEP_INTEGER) {
            $this->step = 1;
        } else if ($this->stepType === self::TYPE_STEP_FLOAT) {
            $this->step = 0.1;
        }

        if (isset($data['options'][0])) {
            foreach ($data['options'] as $optionItem) {
                $this->option[] = new Model\Product\Filter\Option($optionItem);
            }
        }

        // TODO: осторожно, дополнение данных
        if (($this->typeId == self::TYPE_BOOLEAN) && !(bool)$this->option) {
            foreach ([
                ['id' => 1, 'token' => '1', 'name' => 'Да'],
                ['id' => 0, 'token' => '0', 'name' => 'Нет'],
            ] as $optionItem) {
                $option = new Model\Product\Filter\Option();
                $option->id = $optionItem['id'];
                $option->token = $optionItem['token'];
                $option->name = $optionItem['name'];

                $this->option[] = $option;
            }
        } else if (in_array($this->typeId, [self::TYPE_SLIDER, self::TYPE_NUMBER])) {
            foreach ([
                ['id' => $this->min, 'token' => 'from', 'name' => 'От'],
                ['id' => $this->max, 'token' => 'to', 'name' => 'До'],
            ] as $optionItem) {
                $option = new Model\Product\Filter\Option();
                $option->id = $optionItem['id'];
                $option->token = $optionItem['token'];
                $option->name = $optionItem['name'];

                $this->option[] = $option;
            }
        }
     }
}