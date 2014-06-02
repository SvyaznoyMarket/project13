<?php

namespace EnterSite\Model\Partial;

use EnterSite\Model\Partial;

class SelectedFilterBlock extends Partial\Widget {
    /** @var string */
    public $widgetId = 'id-product-selectedFilter';
    /** @var string */
    public $widgetType = 'product-selectedFilter';
    /** @var Partial\ProductFilter[] */
    public $filters = [];
    /** @var bool */
    public $hasFilter;
}