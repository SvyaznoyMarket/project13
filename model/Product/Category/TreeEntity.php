<?php

namespace Model\Product\Category;

class TreeEntity extends BasicEntity {
    /** @var bool|null */
    protected $isFurniture;
    /** @var string|null */
    protected $image;
    /** @var string|null */
    protected $image480x480;
    /** @var int|null */
    protected $productCount;
    /** @var bool|null */
    protected $hasChild;
    /** @var TreeEntity[] */
    protected $child = [];
    /** @var bool|null */
    public $isNew;

    public function __construct(array $data = []) {
        parent::__construct($data);
        
        if (isset($data['id'])) $this->setId($data['id']);
        if (isset($data['uid'])) $this->setUi($data['uid']);
        if (isset($data['parent_id'])) $this->setParentId($data['parent_id']);
        if (isset($data['is_furniture'])) $this->setIsFurniture($data['is_furniture']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['link'])) $this->setLink($data['link']);
        if (isset($data['token'])) $this->setToken($data['token']);

        // Возвращается методом http://search.enter.ru/category/tree
        // Пропускаем url через Source для подмены URL в ветке lite
        if (isset($data['media_image'])) $this->image = (new \Model\Media\Source(['url' => $data['media_image']]))->url;

        // Возвращается методом http://search.enter.ru/category/tree
        // Пропускаем url через Source для подмены URL в ветке lite
        if (isset($data['media_image_480x480'])) $this->image480x480 = (new \Model\Media\Source(['url' => $data['media_image_480x480']]))->url;

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

    /**
     * Рекурсивно находит потомка по uid
     * @param $uid
     *
     * @return TreeEntity|null
     */
    public function findDescendant($uid) {
        $entities = [$this];
        while ($entities) {
            /** @var TreeEntity $entity */
            $entity = array_shift($entities);

            if ($entity->ui === $uid) {
                return $entity;
            }

            $entities = array_merge($entities, $entity->getChild());
        }

        return null;
    }
}