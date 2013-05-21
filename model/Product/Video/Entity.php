<?php

namespace Model\Product\Video;

class Entity {
    /** @var string */
    private $content;

    /** @var string */
    private $maybe3d;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('content', $data)) $this->setContent($data['content']);
        if (array_key_exists('maybe3d', $data)) $this->setMaybe3d($data['maybe3d']);
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

    /**
     * @param string $maybe3d
     */
    public function setMaybe3d($maybe3d)
    {
        $this->maybe3d = $maybe3d;
    }

    /**
     * @return string
     */
    public function getMaybe3d()
    {
        return $this->maybe3d;
    }
}