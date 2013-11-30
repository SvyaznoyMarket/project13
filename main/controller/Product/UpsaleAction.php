<?php

namespace Controller\Product;

class UpsaleAction extends BasicRecommendedAction {
    protected $retailrocketMethodName = 'CrossSellItemToItems';
    protected $actionTitle = 'С этим товаром также покупают';
}