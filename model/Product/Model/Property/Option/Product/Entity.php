<?php

namespace Model\Product\Model\Property\Option\Product;

class Entity {
    use \Model\MediaHostTrait;

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $link;
    /** @var string */
    protected $token;
    /** @var string */
    protected $image;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (string)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
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
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 1) {
        if ($this->image) {
            $urls = \App::config()->productPhoto['url'];

            return $this->getHost() . $urls[$size] . $this->image;
        } else {
            return null;
        }
    }
}