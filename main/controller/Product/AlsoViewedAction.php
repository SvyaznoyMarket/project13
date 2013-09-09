<?php

namespace Controller\Product;

class AlsoViewedAction extends BasicRecommendedAction {

    protected $retailrocketMethodName = 'ItemToItems';
    protected $smartengineMethodName = 'otherusersalsoviewed';
    protected $actionType = 'AlsoViewedAction';
    protected $actionTitle = 'С этим товаром также смотрят';

}