<?php

namespace View\Shop;

class ShowPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $region \Model\Region\Entity */
        $region = $this->getParam('currentRegion') instanceof \Model\Region\Entity ? $this->getParam('currentRegion') : null;
        if (!$region) {
            return;
        }

        /** @var $shop \Model\Shop\Entity */
        $shop = $this->getParam('shop') instanceof \Model\Shop\Entity ? $this->getParam('shop') : null;
        if (!$shop) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Магазины Enter в  ' . $region->getInflectedName(5),
                'url'  => \App::router()->generate('shop.region', array('regionId' => $region->getId())),
            );
            $breadcrumbs[] = array(
                'name' => $shop->getName(),
                'url'  => null, // потому что последний элемент ;)
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $this->setTitle($shop->getName());
        $this->setParam('title', $shop->getName());
    }

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('shop/page-show', $this->params);
    }

    public function slotBodyClassAttribute() {
        return 'shopPrintPage';
    }
}