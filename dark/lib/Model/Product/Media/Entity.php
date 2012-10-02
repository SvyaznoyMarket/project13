<?php

namespace Model\Product\Media;

class Entity {
    CONST TYPE_IMAGE = 1;
    CONST TYPE_3D = 2;

    /** @var int */
    private $id;
    /** @var int */
    private $typeId;
    /** @var string */
    private $name;
    /** @var int */
    private $position;
    /** @var string */
    private $source;
    /** @var string */
    private $ext;
    /** @var int */
    private $fileSize;
    /** @var int */
    private $width;
    /** @var int */
    private $height;
    /** @var string */
    private $host;

    /**
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('position', $data)) $this->setPosition($data['position']);
        if (array_key_exists('source', $data)) $this->setSource($data['source']);
        if (array_key_exists('ext', $data)) $this->setExt($data['ext']);
        if (array_key_exists('filesize', $data)) $this->setFileSize($data['filesize']);
        if (array_key_exists('width', $data)) $this->setWidth($data['width']);
        if (array_key_exists('height', $data)) $this->setHeight($data['height']);

        $this->host = self::getHost($this->id);
    }

    /**
     * @param int $ext
     */
    public function setExt($ext) {
        $this->ext = (string)$ext;
    }

    /**
     * @return string
     */
    public function getExt() {
        return $this->ext;
    }

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize) {
        $this->fileSize = (int)$fileSize;
    }

    /**
     * @return int
     */
    public function getFileSize() {
        return $this->fileSize;
    }

    /**
     * @param int $height
     */
    public function setHeight($height) {
        $this->height = (int)$height;
    }

    /**
     * @return int
     */
    public function getHeight() {
        return $this->height;
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
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = (int)$position;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param string $source
     */
    public function setSource($source) {
        $this->source = (string)$source;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }

    /**
     * @param int $width
     */
    public function setWidth($width) {
        $this->width = (int)$width;
    }

    /**
     * @return int
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getUrl($size = 1) {
        static $urls;
        static $urls3d;

        if (!$urls) $urls = \App::config()->productPhoto['url'];
        if (!$urls3d) $urls3d = \App::config()->productPhoto3d['url'];
        if ($this->typeId == self::TYPE_IMAGE) {
            return $this->host . $urls[$size] . $this->source;
        } else if ($this->typeId == self::TYPE_3D) {
            return $this->host . $urls3d[$size] . $this->source;
        }

        return null;
    }

    static public function getHost($id = null) {
        $hosts = \App::config()->mediaHost['url'];

        $index = $id ? ($id % 10) : rand(0, count($hosts) - 1);
        if (!isset($hosts[$index])) {
            $hosts = array(0 => 'http://fs01.enter.ru');
            $index = 0;
        }

        return $hosts[$index];
    }

}
