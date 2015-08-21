<?php

namespace Model\Product\Category;

use Session\AbTest\ABHelperTrait;

abstract class BasicEntity {
    use ABHelperTrait;

    const PRODUCT_VIEW_COMPACT = 'compact';
    const PRODUCT_VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION = 'light_with_bottom_description';
    const PRODUCT_VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION = 'light_with_hover_bottom_description';
    const PRODUCT_VIEW_LIGHT_WITHOUT_DESCRIPTION = 'light_without_description';
    const PRODUCT_VIEW_EXPANDED = 'expanded';

    /** @var int|null */
    public $id;
    /** @var string|null */
    public $ui;
    /** @var int|null */
    protected $parentId;
    /** @var string|null */
    public $name;
    /** @var string|null */
    protected $link;
    /** @var string|null */
    protected $token;
    /** @var int|null */
    protected $level;

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
     * @return string
     */
    public function getPath() {
        return trim(preg_replace('/^\/catalog\//' , '', $this->link), '/');
    }
}