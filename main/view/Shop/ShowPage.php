<?php

namespace View\Shop;

class ShowPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $shop \Model\Shop\Entity */
        $shop = $this->getParam('shop');
        if (!$shop) {
            return;
        }

        $region = $shop->getRegion();
        if (!$region) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $shopRegionNameInPrepositionalCase = $this->getParam('shopRegionNameInPrepositionalCase');
            $breadcrumbs[] = array(
                'name' => 'Магазины Enter в ' . ($shopRegionNameInPrepositionalCase ? $shopRegionNameInPrepositionalCase : 'городе ' . $region->getName()),
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
        return parent::slotBodyClassAttribute() . ' shopPrintPage';
    }
}