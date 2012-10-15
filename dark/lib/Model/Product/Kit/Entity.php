<?php

namespace Model\Product\Kit;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $count;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('count', $data)) $this->setCount($data['count']);
    }

    /**
     * @param int $count
     */
    public function setCount($count) {
        $this->count = (int)$count;
    }

    /**
     * @return int
     */
    public function getCount() {
        return $this->count;
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
}
