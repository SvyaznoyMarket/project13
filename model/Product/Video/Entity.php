<?php

namespace Model\Product\Video;

class Entity {
    /** @var string */
    private $content;

    /** @var string */
    private $maybe3d;

    /** @var array */
    private $img3d;

    /** @var bool */
    private $pandra;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('content', $data)) $this->setContent($data['content']);
        if (array_key_exists('maybe3d', $data)) $this->setMaybe3d($data['maybe3d']);
        if (array_key_exists('img3d', $data)) $this->setImg3d($data['img3d']);
        if (array_key_exists('pandra', $data)) $this->setPandra($data['pandra']);
    }

    /**
     * @param string $img3d
     */
    public function setImg3d($img3d)
    {
        $this->img3d = $img3d;
    }

    /**
     * @return string
     */
    public function getImg3d()
    {
        return $this->img3d;
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

    /**
     * @param $pandra
     */
    public function setPandra($pandra)
    {
        $this->pandra = (bool) $pandra;
    }

    /**
     * @return bool
     */
    public function getPandra()
    {
        return $this->pandra;
    }
}