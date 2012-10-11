<?php

namespace Model\Product\Category;

class Entity {
    const PRODUCT_VIEW_COMPACT = 'compact';
    const PRODUCT_VIEW_EXPANDED = 'expanded';

    /** @var int */
    private $id;
    /** @var int */
    private $parentId;
    /** @var bool */
    private $isFurniture;
    /** @var string */
    private $name;
    /** @var string */
    private $link;
    /** @var string */
    private $token;
    /** @var string */
    private $image;
    /** @var string */
    private $rootImage;
    /** @var bool */
    private $hasLine;
    /** @var string */
    private $productView;
    /** @var bool */
    private $isInMenu;
    /** @var int */
    private $level;
    /** @var string */
    private $seoTitle;
    /** @var string */
    private $seoKeywords;
    /** @var string */
    private $seoDescription;
    /** @var string */
    private $seoHeader;
    /** @var string */
    private $seoText;
    /** @var int */
    private $productCount;
    /** @var Entity[] */
    private $child = array();
    /** @var Entity|null */
    private $parent;
    /** @var Entity */
    private $root;
    /** @var Entity[] */
    private $ancestor = array();

    public function __construct(array $data = array()) {
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
        return (null != $this->parentId) && (2 == $this->level);
    }

    /**
     * Является ли категория узлом дерева без дочерних элементов (outer node)
     *
     * @return bool
     */
    public function isLeaf() {
        // TODO: это неверное определение
        return $this->level > 2;
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
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
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
     * @param int $level
     */
    public function setLevel($level) {
        $this->level = (int)$level;
    }

    /**
     * @return int
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId) {
        $this->parentId = (int)$parentId;
    }

    /**
     * @return int
     */
    public function getParentId() {
        return $this->parentId;
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
     * @param string $productView
     */
    public function setProductView($productView) {
        if (is_int($productView)) {
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
     * @param Entity[] $child
     */
    public function setChild(array $children) {
        $this->child = array();
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param Entity $child
     */
    public function addChild(Entity $child) {
        $this->child[] = $child;
    }

    /**
     * @return array|Entity[]
     */
    public function getChild() {
        return $this->child;
    }

    /**
     * @param \Model\Product\Category\Entity|null $parent
     */
    public function setParent(Entity $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return \Model\Product\Category\Entity|null
     */
    public function getParent() {
        return $this->parent ?: end($this->ancestor);
    }

    /**
     * @param Entity[] $ancestors
     */
    public function setAncestor(array $ancestors) {
        $this->ancestor = array();
        foreach ($ancestors as $ancestor) {
            $this->addAncestor($ancestor);
        }
    }

    /**
     * @param Entity $ancestor
     */
    public function addAncestor(Entity $ancestor) {
        $this->ancestor[] = $ancestor;
    }

    /**
     * @return Entity[]
     */
    public function getAncestor() {
        return $this->ancestor;
    }

    /**
     * @param \Model\Product\Category\Entity $root
     */
    public function setRoot(Entity $root = null) {
        $this->root = $root;
    }

    /**
     * @return \Model\Product\Category\Entity
     */
    public function getRoot() {
        return $this->root ?: reset($this->ancestor);
    }

    public function getImageUrl($size = 0) {
        if ($this->image) {
            $urls = \App::config()->productCategory['url'];

            return $urls[$size] . $this->image;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/catalog\//' , '', $this->link), '/');
    }
}