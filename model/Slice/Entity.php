<?php

namespace Model\Slice;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $filterQuery;
    /** @var string */
    private $title;
    /** @var string */
    private $metaKeywords;
    /** @var string */
    private $metaDescription;
    /** @var array */
    private $description;
    /** @var string */
    private $productBuyMethod;
    /** @var bool */
    private $showProductState;
    /** @var int */
    private $categoryIds;
    /** @var string */
    private $content;

    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('filter', $data)) $this->setFilterQuery($data['filter']);
        if (array_key_exists('title', $data)) $this->setTitle($data['title']);
        if (array_key_exists('meta_keywords', $data)) $this->setMetaKeywords($data['meta_keywords']);
        if (array_key_exists('meta_description', $data)) $this->setMetaDescription($data['meta_description']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('productBuyMethod', $data)) $this->setProductBuyMethod($data['productBuyMethod']);
        if (array_key_exists('category_id', $data)) $this->setCategoryIds($data['category_id']);
        if (array_key_exists('content', $data)) $this->setContent($data['content']);
        if (array_key_exists('show_state', $data)) {
            $this->setShowProductState($data['show_state']);
        } else {
            $this->setShowProductState(true);
        }
    }

    /**
     * @param array $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return array
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $filterQuery
     */
    public function setFilterQuery($filterQuery) {
        $this->filterQuery = (string)$filterQuery;
    }

    /**
     * @return string
     */
    public function getFilterQuery() {
        return $this->filterQuery;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = (string)$metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = (string)$metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
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
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = (string)$title;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
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
     * @param string $productBuyMethod
     */
    public function setProductBuyMethod($productBuyMethod) {
        $this->productBuyMethod = is_scalar($productBuyMethod) ? (string)$productBuyMethod : null;
    }

    /**
     * @return string
     */
    public function getProductBuyMethod() {
        return $this->productBuyMethod;
    }

    /**
     * @param boolean $showProductState
     */
    public function setShowProductState($showProductState) {
        $this->showProductState = (bool)$showProductState;
    }

    /**
     * @return boolean
     */
    public function getShowProductState() {
        return $this->showProductState;
    }

    /**
     * @param int $categoryIds
     */
    public function setCategoryIds($categoryIds) {
        $this->categoryIds = is_array($categoryIds) ? $categoryIds : [$categoryIds];
    }

    /**
     * @return int
     */
    public function getCategoryIds() {
        return $this->categoryIds;
    }

    /**
     * @return int
     */
    public function getCategoryId() {
        return reset($this->categoryIds);
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