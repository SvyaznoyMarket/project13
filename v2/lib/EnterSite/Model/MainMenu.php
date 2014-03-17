<?php

namespace EnterSite\Model;

class MainMenu {
    const ACTION_SEPARATOR = 'separator';
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT_CATEGORY = 'category';
    const ACTION_PRODUCT_CATALOG = 'catalog';
    const ACTION_PRODUCT = 'product';

    /** @var string */
    public $name;
    /** @var string */
    public $image;
    /** @var string */
    public $action;
    /** @var array */
    public $item;
    /** @var int */
    public $firstItem;
    /** @var MainMenu[] */
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
}
