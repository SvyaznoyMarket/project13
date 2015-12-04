<?php

namespace Model\Slice;

class Entity {
    /** @var string|null */
    private $token;
    /** @var string|null */
    private $name;
    /** @var string|null */
    private $filterQuery;
    /** @var string|null */
    private $title;
    /** @var string|null */
    private $metaKeywords;
    /** @var string|null */
    private $metaDescription;
    /** @var string|null */
    private $description;
    /** @var string|null */
    private $productBuyMethod;
    /** @var bool|null */
    private $showProductState;
    /** @var int|null */
    private $categoryIds;
    /** @var string|null */
    public $categoryUid;
    /** @var string|null */
    private $content;
    /** @var \Model\Brand\Entity[] */
    public $brands = [];

    public function __construct(array $data = []) {
        if (isset($data['token'])) $this->setToken($data['token']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['filter'])) $this->setFilterQuery($data['filter']);
        if (isset($data['title'])) $this->setTitle($data['title']);
        if (isset($data['meta_keywords'])) $this->setMetaKeywords($data['meta_keywords']);
        if (isset($data['meta_description'])) $this->setMetaDescription($data['meta_description']);
        if (isset($data['description'])) $this->setDescription($data['description']);
        if (isset($data['productBuyMethod'])) $this->setProductBuyMethod($data['productBuyMethod']);
        if (isset($data['category_id'])) $this->setCategoryIds($data['category_id']);
        if (isset($data['category_uid'])) $this->categoryUid = $data['category_uid'];
        if (isset($data['content'])) $this->setContent($data['content']);
        if (isset($data['show_state'])) {
            $this->setShowProductState($data['show_state']);
        } else {
            $this->setShowProductState(true);
        }
        
        if (isset($data['brands']) && is_array($data['brands'])) {
            $this->brands = array_map(function($item) {
                return new \Model\Brand\Entity($item);
            }, $data['brands']);
        }
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
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
        return is_array($this->categoryIds) ? reset($this->categoryIds) : $this->categoryIds;
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
     * @param string $token
     * @return \Model\Brand\Entity|null
     */
    public function getBrandByToken($token) {
        foreach ($this->brands as $brand) {
            if ($brand->token === $token) {
                return $brand;
            }
        }
        
        return null;
    }
}