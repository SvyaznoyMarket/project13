<?php

namespace Model\Menu;

class Entity {
    const ACTION_SEPARATOR = 'separator';
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT_CATEGORY = 'category';
    const ACTION_PRODUCT_CATALOG = 'catalog';
    const ACTION_PRODUCT = 'product';

    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var string */
    private $action;
    /** @var array */
    private $item;
    /** @var Entity[] */
    private $child = [];
    /** @var string */
    private $link;
    /** @var string */
    private $color;
    /** @var string */
    private $colorHover;
    /** @var string */
    private $css;
    /** @var string */
    private $cssHover;
    /** @var int */
    private $priority;

    public function __construct(array $data = []) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('image', $data)) $this->setImage($data['image']);
        if (array_key_exists('action', $data)) $this->setAction($data['action']);
        if (array_key_exists('color', $data)) $this->setColor($data['color']);
        if (array_key_exists('colorHover', $data)) $this->setColorHover($data['colorHover']);
        if (array_key_exists('css', $data)) $this->setCss($data['css']);
        if (array_key_exists('cssHover', $data)) $this->setCssHover($data['cssHover']);
        if (array_key_exists('item', $data)) {
            if (!is_array($data['item'])) {
                $data['item'] = [$data['item']];
            }
            $this->setItem($data['item']);
        }
        /*
        if (array_key_exists('child', $data) && is_array($data['child'])) foreach ($data['child'] as $childData) {
            $this->addChild(new Entity($childData));
        }
        */
    }

    /**
     * @param string $action
     */
    public function setAction($action) {
        $this->action = (string)$action;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    public function setChild(array $children) {
        $this->child = [];
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(Entity $child) {
        $this->child[] = $child;
    }

    public function getChild() {
        return $this->child;
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
     * @param array $items
     */
    public function setItem(array $items) {
        $this->item = $items;
    }

    /**
     * @return array
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getFirstItem() {
        return reset($this->item);
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
     * @param string $css
     */
    public function setCss($css) {
        $this->css = (string)$css;
    }

    /**
     * @return string
     */
    public function getCss() {
        return $this->css;
    }

    /**
     * @param string $cssHover
     */
    public function setCssHover($cssHover) {
        $this->cssHover = (string)$cssHover;
    }

    /**
     * @return string
     */
    public function getCssHover() {
        return $this->cssHover;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority) {
        $this->priority = (int)$priority;
    }

    /**
     * @return int
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param string $color
     */
    public function setColor($color) {
        $this->color = (string)$color;
    }

    /**
     * @return string
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * @param string $colorHover
     */
    public function setColorHover($colorHover) {
        $this->colorHover = (string)$colorHover;
    }

    /**
     * @return string
     */
    public function getColorHover() {
        return $this->colorHover;
    }
}