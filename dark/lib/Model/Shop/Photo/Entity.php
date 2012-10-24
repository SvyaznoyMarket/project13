<?php

namespace Model\Shop\Photo;

class Entity {
    /** @var int */
    private $id;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        // TODO: доделать. сейчас ядро ничего не возвращает
        if (array_key_exists('id', $data)) $this->setId($data['id']);
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

    public function getUrl($size = 1) {
        return '';
    }
}