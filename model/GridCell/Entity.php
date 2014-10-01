<?php

namespace Model\GridCell;

class Entity {
    const TYPE_PRODUCT = 'product';
    const TYPE_IMAGE = 'media';
    const TYPE_EMPTY = 'empty';

    /** @var int */
    private $objectId;
    /** @var string */
    private $objectUi;
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
    /** @var string */
    private $url;
    /** @var string */
    private $imageUrl;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (isset($data['col'])) $this->setColumn($data['col']);
        if (isset($data['row'])) $this->setRow($data['row']);
        if (isset($data['sizex'])) $this->setSizeX($data['sizex']);
        if (isset($data['sizey'])) $this->setSizeY($data['sizey']);
        if (isset($data['type'])) $this->setType($data['type']);

        switch ($this->type) {
            case self::TYPE_PRODUCT:
                //$this->setObjectId(isset($data['object']['product']['id']) ? $data['object']['product']['id'] : null);
                $this->setObjectUi(isset($data['object']['product']['uid']) ? $data['object']['product']['uid'] : null);
                break;
            case self::TYPE_IMAGE:
                $this->setUrl(isset($data['object']['link']) ? $data['object']['link'] : null);
                foreach ((isset($data['object']['media']['sources'][0]) ? $data['object']['media']['sources'] : []) as $imageItem) {
                    $imageItem += ['type' => null, 'url' => null];

                    if ('original' == $imageItem['type']) {
                        $this->setImageUrl($imageItem['url']);
                    }
                }

                break;
        }

        if ((self::TYPE_IMAGE == $this->type) && empty($data['object']['media'])) {
            $this->type = self::TYPE_EMPTY;
        }
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
    public function setObjectId($id) {
        $this->objectId = (int)$id;
    }

    /**
     * @return int
     */
    public function getObjectId() {
        return $this->objectId;
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
     * @param string $ui
     */
    public function setObjectUi($ui) {
        $this->objectUi = (string)$ui;
    }

    /**
     * @return string
     */
    public function getObjectUi() {
        return $this->objectUi;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = $url ? (string)$url : null;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl ? (string)$imageUrl : null;
    }

    /**
     * @return string
     */
    public function getImageUrl() {
        return $this->imageUrl;
    }
}
