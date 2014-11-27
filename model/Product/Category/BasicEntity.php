<?php

namespace Model\Product\Category;

class BasicEntity {
    const PRODUCT_VIEW_COMPACT = 'compact';
    const PRODUCT_VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION = 'light_with_bottom_description';
    const PRODUCT_VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION = 'light_with_hover_bottom_description';
    const PRODUCT_VIEW_LIGHT_WITHOUT_DESCRIPTION = 'light_without_description';
    const PRODUCT_VIEW_EXPANDED = 'expanded';

    /** @var int */
    protected $id;
    /** @var string|null */
    protected $ui;
    /** @var int */
    protected $parentId;
    /** @var string */
    protected $name;
    /** @var string */
    protected $link;
    /** @var string */
    protected $token;
    /** @var string */
    protected $image;
    /** @var int */
    protected $level;
    /** @var BasicEntity[] */
    protected $child = [];
    /** @var BasicEntity|null */
    protected $parent;
    /** @var BasicEntity */
    protected $root;
    /** @var BasicEntity[] */
    protected $ancestor = [];

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('ui', $data)) $this->setUi($data['ui']);
        if (array_key_exists('uid', $data)) $this->setUi($data['uid']); // http://api.enter.ru/v2/category/tree возвращает uid
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('level', $data)) $this->setLevel($data['level']);
        if (array_key_exists('children', $data) && is_array($data['children'])) foreach ($data['children'] as $childData) {
            $this->addChild(new BasicEntity($childData));
        }
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
     * @param string $ui
     */
    public function setUi($ui) {
        $this->ui = (string)$ui;
    }

    /**
     * @return string|null
     */
    public function getUi() {
        return $this->ui;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = rtrim((string)$link, '/');
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
     * @param BasicEntity[] $children
     */
    public function setChild(array $children) {
        $this->child = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param BasicEntity $child
     */
    public function addChild(BasicEntity $child) {
        $this->child[] = $child;
    }

    /**
     * @return array|BasicEntity[]
     */
    public function getChild() {
        return $this->child;
    }

    /**
     * @param \Model\Product\Category\BasicEntity|null $parent
     */
    public function setParent(BasicEntity $parent = null) {
        $this->parent = $parent;
    }

    /**
     * @return \Model\Product\Category\BasicEntity|null
     */
    public function getParent() {
        return $this->parent ?: end($this->ancestor);
    }

    /**
     * @param BasicEntity[] $ancestors
     */
    public function setAncestor(array $ancestors) {
        $this->ancestor = [];
        foreach ($ancestors as $ancestor) {
            $this->addAncestor($ancestor);
        }
    }

    /**
     * @param BasicEntity $ancestor
     */
    public function addAncestor(BasicEntity $ancestor) {
        $this->ancestor[] = $ancestor;
    }

    /**
     * @return BasicEntity[]
     */
    public function getAncestor() {
        return $this->ancestor;
    }

    /**
     * @param \Model\Product\Category\BasicEntity $root
     */
    public function setRoot(BasicEntity $root = null) {
        $this->root = $root;
    }

    /**
     * @return \Model\Product\Category\BasicEntity
     */
    public function getRoot() {
        return $this->root ?: reset($this->ancestor);
    }

    /**
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/catalog\//' , '', $this->link), '/');
    }

    public function getImageUrl($size = 0) {
        if ($this->image) {
            $urls = \App::config()->productCategory['url'];

            return $this->getHost() . $urls[$size] . $this->image;
        } else {
            return null;
        }
    }

    public function isAppliancesRoot() {
        return ('616e6afd-fd4d-4ff4-9fe1-8f78236d9be6' === $this->getUi());
    }

    public function isAppliances() {
        $root = $this->getRoot();
        if ($root) {
            return ('616e6afd-fd4d-4ff4-9fe1-8f78236d9be6' === $root->getUi());
        }

        return false;
    }
}