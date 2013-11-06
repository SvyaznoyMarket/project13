<?php

namespace View\Slice;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $slice \Model\Slice\Entity */
        $slice = $this->getParam('slice') instanceof \Model\Slice\Entity ? $this->getParam('slice') : null;
        if (!$slice) {
            return;
        }

        $this->setTitle($slice->getTitle());
        $this->addMeta('description', $slice->getMetaDescription());
        $this->addMeta('keywords', $slice->getMetaKeywords());
    }

    public function slotContent() {
        return $this->render('slice/page-show', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }
}
