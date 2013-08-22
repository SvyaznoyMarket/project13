<?php

namespace Controller\Product;

class SimilarAction extends BasicRecommendedAction {

    protected $retailrocketMethodName = 'ItemToItems';
    protected $smartengineMethodName = 'relateditems';
    protected $actionType = 'SimilarAction';
    protected $actionTitle = 'Похожие товары';

}