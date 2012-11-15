<?php

namespace Model\Order\Interval;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $start;
    /** @var string */
    private $end;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('time_begin', $data)) $this->setStart($data['time_begin']);
        if (array_key_exists('time_end', $data)) $this->setEnd($data['time_end']);
    }

    /**
     * @param string $end
     */
    public function setEnd($end) {
        $this->end = $end ? (string)$end : null;
    }

    /**
     * @return string
     */
    public function getEnd() {
        return $this->end;
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
     * @param string $start
     */
    public function setStart($start) {
        $this->start = $start ? (string)$start : null;
    }

    /**
     * @return string
     */
    public function getStart() {
        return $this->start;
    }
}