<?php

namespace Model\Product;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $viewId;
    /** @var int */
    private $typeId;
    /** @var int */
    private $setId;
    /** @var int */
    private $labelId;
    /** @var bool */
    private $isModel;
    /** @var bool */
    private $isPrimaryLine;
    /** @var int */
    private $modelId;
    /** @var int|null */
    private $score;
    /** @var string */
    private $name;
    /** @var string */
    private $link;
    /** @var string */
    private $token;
    /** @var string */
    private $webName;
    /** @var string */
    private $prefix;
    /** @var string */
    private $article;
    /** @var string */
    private $barcode;
    /** @var string */
    private $tagline;
    /** @var string */
    private $announce;
    /** @var string */
    private $description;
    /** @var string */
    private $image;
    /** @var int */
    private $rating;
    /** @var int */
    private $ratingCount;
    /** @var Category\Entity[] */
    private $category;
    /** @var int */
    private $connectedViewId;
    /** @var Property\Group\Entity[] */
    private $propertyGroup;
    /** @var Property\Entity[] */
    private $property;
    /** @var \Model\Tag\Entity[] */
    private $tag;
    /** @var Media\Entity[] */
    private $media;
    /** @var int */
    private $commentCount;
    /** @var int */
    private $price;
    /** @var int */
    private $priceAverage;
    /** @var int */
    private $priceOld;
    /** @var State\Entity[] */
    private $state;
    /** @var Stock\Entity[] */
    private $stock;

    public function __construct(array $data = array()) {
        $this->category = array();
        $this->propertyGroup = array();
        $this->property = array();
        $this->tag = array();
        $this->media = array();
        $this->state = array();
        $this->stock = array();

        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('category', $data) && is_array($data['category'])) $this->setCategory(array_map(function($data) {
            return new Category\Entity($data);
        }, $data['category']));
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
     * @param Category\Entity $categories
     */
    public function setCategory(array $categories) {
        $this->category = array();
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
    }

    /**
     * @param Category\Entity $category
     */
    public function addCategory(Category\Entity $category) {
        $this->category[] = $category;
    }

    /**
     * @return Category\Entity[]
     */
    public function getCategory() {
        return $this->category;
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
     * @return Category\Entity
     */
    public function getMainCategory() {
        return reset($this->category);
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
}