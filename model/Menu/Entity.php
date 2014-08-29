<?php

namespace Model\Menu;

class Entity {
    const ACTION_SEPARATOR = 'separator';
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT_CATEGORY = 'category';
    const ACTION_PRODUCT_ALL_CATEGORIES = 'all_categories';
    const ACTION_PRODUCT_CATALOG = 'catalog';
    const ACTION_PRODUCT = 'product';

    /** @var string */
    public $name;
    /** @var string */
    public $image;
    /** @var string */
    public $smallImage;
    /** @var string */
    public $action;
    /** @var array */
    public $item;
    /** @var int */
    public $firstItem;
    /** @var Entity[] */
    public $child = [];
    /** @var string */
    public $link;
    /** @var string */
    public $color;
    /** @var string */
    public $colorHover;
    /** @var string */
    public $css;
    /** @var string */
    public $cssHover;
    /** @var int */
    public $priority;
    /** @var string */
    public $titleCss;
    /** @var string */
    public $titleHoverCss;

    public function __construct(array $data = []) {
        if (isset($data['name'])) $this->name = (string)$data['name'];
        if (isset($data['image'])) $this->image = (string)$data['image'];
        if (isset($data['smallImage'])) $this->smallImage = (string)$data['smallImage'];
        if (isset($data['action'])) $this->action = (string)$data['action'];
        if (isset($data['color'])) $this->color = (string)$data['color'];
        if (isset($data['colorHover'])) $this->colorHover = (string)$data['colorHover'];
        if (isset($data['css'])) $this->css = (string)$data['css'];
        if (isset($data['cssHover'])) $this->cssHover = (string)$data['cssHover'];
        if (isset($data['titleCss'])) $this->titleCss = (string)$data['titleCss'];
        if (isset($data['titleHoverCss'])) $this->titleHoverCss = (string)$data['titleHoverCss'];
        if (array_key_exists('item', $data)) {
            if (!is_array($data['item'])) {
                $data['item'] = [$data['item']];
            }
            $this->item = $data['item'];
            $this->firstItem = reset($this->item);
        }
    }
}