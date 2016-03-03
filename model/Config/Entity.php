<?php

namespace Model\Config;

class Entity
{
    /** @var string */
    public $name;
    /** @var mixed */
    public $value;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('key', $data)) $this->name = $data['key'];
        if (array_key_exists('value', $data)) $this->value = $data['value'];

        if (0 === strpos($this->value, '{"')) {
            $this->value = json_decode($this->value, true);
        }
    }
}