<?php

namespace Model\Product\Model\Property;

class Entity {
    /** @var int|null */
    public $id;
    /** @var string */
    public $name = '';
    /** @var Option\Entity[] */
    public $option = [];

    public function __construct($data = []) {
        if (isset($data['property']['id'])) $this->id = (int)$data['property']['id'];
        if (isset($data['property']['name'])) $this->name = (string)$data['property']['name'];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $this->option[] = new Option\Entity($item);
            }
        }
    }
}
