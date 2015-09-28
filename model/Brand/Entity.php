<?php

namespace Model\Brand;

use Model\Media;

class Entity {

    const UI_TCHIBO = '73b7d62f-67ae-11e3-93ee-e4115baba630';

    /** @var string */
    private $ui;
    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var Media[]  */
    private $medias = [];

    public function __construct(array $data = []) {
        if (array_key_exists('ui', $data)) $this->ui = $data['ui'];
        if (array_key_exists('id', $data)) $this->id = $data['id'];
        if (array_key_exists('token', $data)) $this->token = $data['token'];
        if (array_key_exists('name', $data)) $this->name = $data['name'];
        if (array_key_exists('medias', $data) && is_array($data['medias'])) {
            $this->medias = array_map(function($arr){ return new Media($arr); }, $data['medias']);
        }
        // set default (small) image
        foreach ($this->medias as $media) {
            if (in_array('small', $media->tags, true)) {
                $this->image = $media->getOriginalImage();
            }
        }
    }

    /**
     * @return string
     */
    public function getUi() {
        return $this->ui;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function isTchibo() {
        return $this->ui === self::UI_TCHIBO;
    }
}