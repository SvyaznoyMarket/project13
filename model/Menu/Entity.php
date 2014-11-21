<?php

namespace Model\Menu;

class Entity {
    const ACTION_SEPARATOR = 'separator';
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT_CATEGORY = 'category-get';
    const ACTION_PRODUCT_CATALOG = 'category-tree';
    const ACTION_PRODUCT = 'product';
    const ACTION_SLICE = 'slice';

    /** @var string */
    public $id;
    /** @var string */
    public $type;
    /** @var string */
    public $name;
    /** @var string */
    public $char;
    /** @var string */
    public $image;
    /** @var string */
    public $class;
    /** @var int */
    public $level;
    /** @var string */
    public $smallImage;
    /** @var bool|null */
    public $useLogo;
    /** @var string|null */
    public $logoPath;
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
        if (isset($data['char'])) $this->char = (string)$data['char'];
        if (isset($data['image'])) $this->image = (string)$data['image'];
        if (isset($data['smallImage'])) $this->smallImage = (string)$data['smallImage'];
        if (isset($data['useLogo'])) $this->useLogo = (bool)$data['useLogo'];
        if (isset($data['logoPath'])) $this->logoPath = (string)$data['logoPath'];
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

        $data += ['media' => []];
        foreach ($data['media'] as $mediaItem) {
            $mediaItem += ['provider' => null, 'sources' => []];
            if ('image' == $mediaItem['provider']) {
                if ($sourceItem = reset($mediaItem['sources'])) {
                    $this->image = @$sourceItem['url'];
                }
            }
        }
    }
}