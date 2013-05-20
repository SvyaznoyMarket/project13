<?php

namespace Model\Product\Video;

class Entity {
    /** @var string */
    private $content;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('content', $data)) $this->setContent($data['content']);
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = (string)$content;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}