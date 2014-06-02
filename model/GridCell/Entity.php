<?php

namespace Model\GridCell;

class Entity {
    const TYPE_PRODUCT = 'product';
    const TYPE_IMAGE = 'image';

    /** @var int */
    private $id;
    /** @var string */
    private $ui;
    /** @var int */
    private $column;
    /** @var int */
    private $row;
    /** @var int */
    private $sizeX;
    /** @var int */
    private $sizeY;
    /** @var string */
    private $type;
    /** @var array */
    private $content = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('col', $data)) $this->setColumn($data['col']);
        if (array_key_exists('row', $data)) $this->setRow($data['row']);
        if (array_key_exists('size_x', $data)) $this->setSizeX($data['size_x']);
        if (array_key_exists('size_y', $data)) $this->setSizeY($data['size_y']);
        if (isset($data['meta']['id'])) $this->setId($data['meta']['id']);
        if (isset($data['meta']['ui'])) $this->setUi($data['meta']['ui']);
        if (isset($data['meta']['type'])) $this->setType($data['meta']['type']);
        if (array_key_exists('meta', $data) && is_array($data['meta'])) $this->setContent($data['meta']);
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
     * @param int $sizeX
     */
    public function setSizeX($sizeX) {
        $this->sizeX = (int)$sizeX;
    }

    /**
     * @return int
     */
    public function getSizeX() {
        return $this->sizeX;
    }

    /**
     * @param int $sizeY
     */
    public function setSizeY($sizeY) {
        $this->sizeY = (int)$sizeY;
    }

    /**
     * @return int
     */
    public function getSizeY() {
        return $this->sizeY;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content) {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = (string)$ui;
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
    }
}
