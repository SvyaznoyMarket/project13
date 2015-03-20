<?php

namespace View\Jewel\ProductCategory;

abstract class Layout extends \View\ProductCategory\Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' jewel';
    }

    public function slotContentHead() {
        $ret = '';

        // заголовок контента страницы - убираем, его роль выполняет баннер из сервиса контента
        $this->setParam('title', null);

        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        $categoryData = $this->tryRender('product-category/_categoryData', array('page' => $this, 'category' => $this->getParam('category')));
        $contentHead = $this->render('_contentHead', $this->params);

        if ($categoryData) $ret .= $categoryData;
        if ($contentHead) $ret .= $contentHead;

        return $ret;
    }

    public function getBreadcrumbsPath() {
        $category = $this->getParam('category');
        if (!($category instanceof \Model\Product\Category\Entity)) {
            return [];
        }

        $breadcrumbs = [];
        foreach ($category->getAncestor() as $ancestor) {
            $breadcrumbs[] = array(
                'name' => $ancestor->getName(),
                'url'  => $ancestor->getLink(),
            );
        }

        $breadcrumbs[] = array(
            'name' => $category->getName(),
            'url'  => $category->getLink(),
        );

        return $breadcrumbs;
    }

    public function slotMetaOg() {}
}