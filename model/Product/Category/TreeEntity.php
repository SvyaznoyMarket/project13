<?php

namespace Model\Product\Category;

class TreeEntity extends BasicEntity {
    /** @var bool|null */
    protected $isFurniture;
    /** @var string|null */
    protected $image;
    /** @var string|null */
    protected $image480x480;
    /** @var string|null */
    protected $productView;
    /** @var int|null */
    protected $productCount;
    /** @var bool|null */
    protected $hasChild;
    /** @var TreeEntity[] */
    protected $child = [];
    /** @var bool|null */
    public $isNew;

    public function __construct(array $data = []) {
        if (isset($data['id'])) $this->setId($data['id']);
        if (isset($data['uid'])) $this->setUi($data['uid']);
        if (isset($data['parent_id'])) $this->setParentId($data['parent_id']);
        if (isset($data['is_furniture'])) $this->setIsFurniture($data['is_furniture']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['link'])) $this->setLink($data['link']);
        if (isset($data['token'])) $this->setToken($data['token']);
        if (isset($data['media_image'])) $this->image = $data['media_image']; // Возвращается методом http://search.enter.ru/category/tree
        if (isset($data['media_image_480x480'])) $this->image480x480 = $data['media_image_480x480']; // Возвращается методом http://search.enter.ru/category/tree
        if (isset($data['product_view_id'])) $this->setProductView($data['product_view_id']);
        if (isset($data['level'])) $this->setLevel($data['level']);
        if (isset($data['product_count'])) $this->setProductCount($data['product_count']);
        if (isset($data['has_children'])) $this->setHasChild($data['has_children']);
        if (isset($data['is_new'])) $this->isNew = $data['is_new'];

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $childData) {
                if (is_array($childData)) {
                    $this->addChild(new TreeEntity($childData));
                }
            }
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
            if (0 == $size) {
                return $this->image;
            } else if (3 == $size) {
                return $this->image480x480;
            }
        }

        return null;
    }

    /**
     * @param TreeEntity $child
     */
    public function addChild(TreeEntity $child) {
        $this->child[] = $child;
    }

    /**
     * @return TreeEntity[]
     */
    public function getChild() {
        return $this->child;
    }
}