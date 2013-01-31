<?php

namespace Model\Shop\Photo;

class Entity {
    /** @var string */
    private $source;
    /** @var int */
    private $position;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('source', $data)) $this->setSource($data['source']);
        if (array_key_exists('position', $data)) $this->setPosition($data['position']);
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
     * @param int $size
     * @return null|string
     */
    public function getUrl($size = 1) {
        if ($this->source) {
            $urls = \App::config()->shopPhoto['url'];

            return $urls[$size] . $this->source;
        } else {
            return null;
        }
    }
}