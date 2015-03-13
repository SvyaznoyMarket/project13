<?php

namespace Controller\Product;

class AlsoViewedAction extends BasicRecommendedAction {

    protected $retailrocketMethodName = 'ItemToItems';
    protected $actionType = 'AlsoViewedAction';
    protected $actionTitle = 'С этим товаром также смотрят';

}