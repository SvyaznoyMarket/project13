<?php

namespace Model\Shop\Panorama;

class Entity {
    /* @var int */
    private $id;
    /* @var string */
    private $swf;
    /* @var string */
    private $xml;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('swf', $data)) $this->setSwf($data['swf']);
        if (array_key_exists('xml', $data)) $this->setXml($data['xml']);
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
     * @param string $swf
     */
    public function setSwf($swf) {
        $this->swf = (string)$swf;
    }

    /**
     * @return string
     */
    public function getSwf() {
        return $this->swf;
    }

    /**
     * @param string $xml
     */
    public function setXml($xml) {
        $this->xml = (string)$xml;
    }

    /**
     * @return string
     */
    public function getXml() {
        return $this->xml;
    }
}
