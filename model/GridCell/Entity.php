<?php

namespace Model\GridCell;

class Entity {
    const TYPE_PRODUCT = 'product';
    const TYPE_IMAGE = 'image';

    /** @var int */
    private $id;
    /** @var int */
    private $column;
    /** @var int */
    private $row;
    /** @var int */
    private $x;
    /** @var int */
    private $y;
    /** @var string */
    private $type;

    public function __construct(array $data = []) {
        if (array_key_exists('col', $data)) $this->setColumn($data['col']);
        if (array_key_exists('row', $data)) $this->setRow($data['row']);
        if (array_key_exists('x', $data)) $this->setX($data['x']);
        if (array_key_exists('y', $data)) $this->setY($data['y']);
        if (isset($data['meta']['id'])) $this->setId($data['meta']['id']);
        if (isset($data['meta']['type'])) $this->setType($data['meta']['type']);
    }

    /**
     * @param int $column
     */
    public function setColumn($column) {
        $this->column = (int)$column;
    }

    /**
     * @return int
     */
    public function getColumn() {
        return $this->column;
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
     * @param int $row
     */
    public function setRow($row) {
        $this->row = (int)$row;
    }

    /**
     * @return int
     */
    public function getRow() {
        return $this->row;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param int $x
     */
    public function setX($x) {
        $this->x = (int)$x;
    }

    /**
     * @return int
     */
    public function getX() {
        return $this->x;
    }

    /**
     * @param int $y
     */
    public function setY($y) {
        $this->y = (int)$y;
    }

    /**
     * @return int
     */
    public function getY() {
        return $this->y;
    }
}
