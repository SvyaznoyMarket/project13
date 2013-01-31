<?php

namespace Model\Product\Category;

class BasicEntity {
    const PRODUCT_VIEW_COMPACT = 'compact';
    const PRODUCT_VIEW_EXPANDED = 'expanded';

    /** @var int */
    protected $id;
    /** @var int */
    protected $parentId;
    /** @var string */
    protected $name;
    /** @var string */
    protected $link;
    /** @var string */
    protected $token;
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
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
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
}