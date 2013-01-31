<?php

namespace Model\Product\Category;

class TreeEntity extends BasicEntity {
    /** @var bool */
    protected $isFurniture;
    /** @var string */
    protected $image;
    /** @var string */
    protected $rootImage;
    /** @var bool */
    protected $hasLine;
    /** @var string */
    protected $productView;
    /** @var bool */
    protected $isInMenu;
    /** @var string */
    protected $seoTitle;
    /** @var string */
    protected $seoKeywords;
    /** @var string */
    protected $seoDescription;
    /** @var string */
    protected $seoHeader;
    /** @var string */
    protected $seoText;
    /** @var int */
    protected $productCount;
    /** @var int */
    protected $globalProductCount;
    /** @var bool */
    protected $hasChild;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('is_furniture', $data)) $this->setIsFurniture($data['is_furniture']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('has_line', $data)) $this->setHasLine($data['has_line']);
        if (array_key_exists('product_view_id', $data)) $this->setProductView($data['product_view_id']);
        if (array_key_exists('is_shown_in_menu', $data)) $this->setIsInMenu($data['is_shown_in_menu']);
        if (array_key_exists('level', $data)) $this->setLevel($data['level']);
        if (array_key_exists('seo_title', $data)) $this->setSeoTitle($data['seo_title']);
        if (array_key_exists('seo_keywords', $data)) $this->setSeoKeywords($data['seo_keywords']);
        if (array_key_exists('seo_description', $data)) $this->setSeoDescription($data['seo_description']);
        if (array_key_exists('seo_header', $data)) $this->setSeoHeader($data['seo_header']);
        if (array_key_exists('seo_text', $data)) $this->setSeoText($data['seo_text']);
        if (array_key_exists('root_category_image', $data)) $this->setRootImage($data['root_category_image']);
        if (array_key_exists('product_count', $data)) $this->setProductCount($data['product_count']);
        if (array_key_exists('product_count_global', $data)) $this->setGlobalProductCount($data['product_count_global']);
        if (array_key_exists('has_children', $data)) $this->setHasChild($data['has_children']);

        if (array_key_exists('children', $data) && is_array($data['children'])) foreach ($data['children'] as $childData) {
            $this->addChild(new Entity($childData));
        }
    }

    /**
     * Является ли категория корневым узлом дерева (root node)
     *
     * @return bool
     */
    public function isRoot() {
        return 1 == $this->level;
    }

    /**
     * Является ли категория узлом дерева с дочерними элементами (inner node)
     *
     * @return bool
     */
    public function isBranch() {
        return $this->hasChild;
    }

    /**
     * Является ли категория узлом дерева без дочерних элементов (outer node)
     *
     * @return bool
     */
    public function isLeaf() {
        return !$this->hasChild;
    }

    /**
     * @param boolean $hasLine
     */
    public function setHasLine($hasLine) {
        $this->hasLine = (bool)$hasLine;
    }

    /**
     * @return boolean
     */
    public function getHasLine() {
        return $this->hasLine;
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
     * @param boolean $isFurniture
     */
    public function setIsFurniture($isFurniture) {
        $this->isFurniture = (bool)$isFurniture;
    }

    /**
     * @return boolean
     */
    public function getIsFurniture() {
        return $this->isFurniture;
    }

    /**
     * @param boolean $isInMenu
     */
    public function setIsInMenu($isInMenu) {
        $this->isInMenu = (bool)$isInMenu;
    }

    /**
     * @return boolean
     */
    public function getIsInMenu() {
        return $this->isInMenu;
    }

    /**
     * @param int $productCount
     */
    public function setProductCount($productCount)
    {
        $this->productCount = (int)$productCount;
    }

    /**
     * @return int
     */
    public function getProductCount()
    {
        return $this->productCount;
    }

    /**
     * @param int $globalProductCount
     */
    public function setGlobalProductCount($globalProductCount) {
        $this->globalProductCount = (int)$globalProductCount;
    }

    /**
     * @return int
     */
    public function getGlobalProductCount() {
        return $this->globalProductCount;
    }

    /**
     * @param string $productView
     */
    public function setProductView($productView) {
        if ((int)$productView > 0) {
            if (1 == $productView) {
                $this->productView = self::PRODUCT_VIEW_COMPACT;
            } else if (2 == $productView) {
                $this->productView = self::PRODUCT_VIEW_EXPANDED;
            }
        } else {
            $this->productView = (string)$productView;
        }
    }

    /**
     * @return string
     */
    public function getProductView() {
        return $this->productView;
    }

    /**
     * @param string $rootImage
     */
    public function setRootImage($rootImage) {
        $this->rootImage = (string)$rootImage;
    }

    /**
     * @return string
     */
    public function getRootImage() {
        return $this->rootImage;
    }

    /**
     * @param string $seoDescription
     */
    public function setSeoDescription($seoDescription) {
        $this->seoDescription = (string)$seoDescription;
    }

    /**
     * @return string
     */
    public function getSeoDescription() {
        return $this->seoDescription;
    }

    /**
     * @param string $seoHeader
     */
    public function setSeoHeader($seoHeader) {
        $this->seoHeader = (string)$seoHeader;
    }

    /**
     * @return string
     */
    public function getSeoHeader() {
        return $this->seoHeader;
    }

    /**
     * @param string $seoKeywords
     */
    public function setSeoKeywords($seoKeywords) {
        $this->seoKeywords = (string)$seoKeywords;
    }

    /**
     * @return string
     */
    public function getSeoKeywords() {
        return $this->seoKeywords;
    }

    /**
     * @param string $seoText
     */
    public function setSeoText($seoText) {
        $this->seoText = (string)$seoText;
    }

    /**
     * @return string
     */
    public function getSeoText() {
        return $this->seoText;
    }

    /**
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle) {
        $this->seoTitle = (string)$seoTitle;
    }

    /**
     * @return string
     */
    public function getSeoTitle() {
        return $this->seoTitle;
    }

    /**
     * @param bool $hasChild
     */
    public function setHasChild($hasChild) {
        $this->hasChild = (bool)$hasChild;
    }

    /**
     * @return bool
     */
    public function getHasChild() {
        return $this->hasChild;
    }

    public function getImageUrl($size = 0) {
        if ($this->image) {
            $urls = \App::config()->productCategory['url'];

            return $urls[$size] . $this->image;
        } else {
            return null;
        }
    }
}