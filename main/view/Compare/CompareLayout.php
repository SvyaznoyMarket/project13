<?php
namespace View\Compare;

class CompareLayout extends \View\DefaultLayout {
    protected $layout = 'layout-compare';

    public function slotContent() {
        return \App::closureTemplating()->render('compare/content', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'compare';
    }

    public function slotUserbarContent() {
        return $this->render('userbar/_defaultContent');
    }

    public function slotHubrusJS() {
        $products = $this->getParam('products');
        if (empty($products)) return '';
        return parent::slotHubrusJS() . \View\Partners\Hubrus::addHubrusData('compared_items', $products);
    }
}