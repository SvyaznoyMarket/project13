<?php

namespace View\Jewel\ProductCategory;

abstract class Layout extends \View\ProductCategory\Layout {
    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' jewel';
    }

    public function slotContentHead() {
        $ret = '';

        // заголовок контента страницы - убираем, его роль выполняет баннер из сервиса контента
        $this->setParam('title', null);

        $contentHead = $this->render('_contentHead', array_merge($this->params, [
            'showAllBreadcrumbs' => true,
        ]));

        if ($contentHead) $ret .= $contentHead;

        return $ret;
    }

    public function slotMetaOg() {}
}