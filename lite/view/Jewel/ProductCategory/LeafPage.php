<?php

namespace view\Jewel\ProductCategory;


class LeafPage extends \View\ProductCategory\LeafPage
{
    public function prepare()
    {
        parent::prepare();
        $this->setParam('jewelClass', 'content-jewel');
    }

}