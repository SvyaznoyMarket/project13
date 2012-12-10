<?php

namespace Model\Product;

class BasicEntity {
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
    /** @var int */
    protected $price;
    /** @var State\Entity */
    protected $state;
    /** @var Line\Entity */
    protected $line;
    /** @var Category\Entity */
    protected $mainCategory;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('category', $data) && (bool)$data['category']) {
            $categoryData = reset($data['category']);
            $this->setMainCategory(new Category\Entity($categoryData));
        };
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('state', $data) && (bool)$data['state']) $this->setState(new State\Entity($data['state']));
        if (array_key_exists('line', $data) && (bool)$data['line']) $this->setLine(new Line\Entity($data['line']));
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
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = (int)$price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param State\Entity $state
     */
    public function setState(State\Entity $state = null) {
        $this->state = $state;
    }

    /**
     * @return State\Entity
     */
    public function getState() {
        return $this->state;
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

            return Media\Entity::getHost($this->id) . $urls[$size] . $this->image;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getNameWithCategory() {
        $name = $this->name;

        if ($this->mainCategory) {
            $name .= ' - ' . $this->mainCategory->getName();
        }

        return $name;
    }

    /**
     * @param \Model\Product\Category\Entity $mainCategory
     */
    public function setMainCategory(Category\Entity $mainCategory = null) {
        $this->mainCategory = $mainCategory;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getMainCategory() {
        return $this->mainCategory;
    }

    /**
     * @return bool
     */
    public function getIsBuyable() {
        return $this->getState() && $this->getState()->getIsBuyable() && $this->getState()->getIsStore();
    }

    /**
     * @param \Model\Line\Entity $line
     */
    public function setLine(Line\Entity $line = null) {
        $this->line = $line;
    }

    /**
     * @return Line\Entity
     */
    public function getLine() {
        return $this->line;
    }
}