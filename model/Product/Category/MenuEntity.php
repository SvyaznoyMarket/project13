<?php

namespace Model\Product\Category;

class MenuEntity {
    use \Model\MediaHostTrait;

    const MAX_CHILD = 10;

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
    /** @var int */
    protected $level;
    /** @var string */
    protected $image;
    /** @var bool|null */
    public $useLogo;
    /** @var string|null */
    public $logoPath;
    /** @var MenuEntity[] */
    protected $child = [];
    /** @var int */
    protected $childCount = 0;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('ui', $data)) $this->setUi($data['ui']); // http://api.enter.ru/v2/category/get возвращает ui
        if (array_key_exists('uid', $data)) $this->setUi($data['uid']); // http://api.enter.ru/v2/category/tree возвращает uid
        if (array_key_exists('parent_id', $data)) $this->setParentId($data['parent_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('level', $data)) $this->setLevel($data['level']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('use_logo', $data)) $this->setUseLogo($data['use_logo']);
        if (array_key_exists('logo_path', $data)) $this->setLogoPath($data['logo_path']);
        if (array_key_exists('children', $data) && is_array($data['children'])) {
            $this->childCount = count($data['children']);

            $limit = (2 == $data['level'])
                ? ($this->childCount < self::MAX_CHILD ? $this->childCount : self::MAX_CHILD)
                : $this->childCount;
            for ($i = 0; $i < $limit; $i++) {
                $this->addChild(new MenuEntity($data['children'][$i]));
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

    /**
     * @param bool $useLogo
     */
    public function setUseLogo($useLogo) {
        $this->useLogo = (bool)$useLogo;
    }

    /**
     * @return bool|null
     */
    public function getUseLogo() {
        return $this->useLogo;
    }

    /**
     * @param string $logoPath
     */
    public function setLogoPath($logoPath) {
        $this->logoPath = (string)$logoPath;
    }

    /**
     * @return string|null
     */
    public function getLogoPath() {
        return $this->logoPath;
    }

    public function getImageUrl($size = 0) {
        static $urls;

        if (!$urls) $urls = \App::config()->productCategory['url'];

        if ($this->image) {
            if (preg_match('/^(https?|ftp)\:\/\//i', $this->image)) {
                return $this->image;
            } else {
                return $this->getHost() . $urls[$size] . $this->image;
            }
        } else {
            return null;
        }
    }
}