<?php

namespace Model\Product\Category;

class MenuEntity {
    const MAX_CHILD = 5;

    /** @var int */
    protected $id;
    /** @var int */
    protected $parentId;
    /** @var string */
    protected $name;
    /** @var string */
    protected $link;
    /** @var int */
    protected $level;
    /** @var string */
    protected $image;
    /** @var MenuEntity[] */
    protected $child = [];
    /** @var int */
    protected $childCount = 0;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('level', $data)) $this->setLevel($data['level']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('children', $data) && is_array($data['children'])) {
            $this->childCount = count($data['children']);
            $i = 1;
            foreach ($data['children'] as $childData) {
                if ((self::MAX_CHILD < $i) && (2 == $data['level'])) break;
                $this->addChild(new MenuEntity($childData));
                $i++;
            }
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
     * @param MenuEntity[] $children
     */
    public function setChild(array $children) {
        $this->child = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param MenuEntity $child
     */
    public function addChild(MenuEntity $child) {
        $this->child[$child->getId()] = $child;
    }

    /**
     * @return array|MenuEntity[]
     */
    public function getChild() {
        return $this->child;
    }

    /**
     * @return int
     */
    public function countChild() {
        return $this->childCount;
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
        return rtrim($this->link, '/');
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
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/catalog\//' , '', $this->link), '/');
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

    public function getImageUrl($size = 0) {
        if ($this->image) {
            $urls = \App::config()->productCategory['url'];

            return $urls[$size] . $this->image;
        } else {
            return null;
        }
    }
}