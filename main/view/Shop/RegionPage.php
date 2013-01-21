<?php

namespace View\Shop;

class RegionPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $region \Model\Region\Entity */
        $region = $this->getParam('currentRegion') instanceof \Model\Region\Entity ? $this->getParam('currentRegion') : null;
        /*if (!$region) {
            return;
        }*/

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => $region ? ('Магазины Enter в  ' . $region->getInflectedName(5)) : 'Все магазины Enter',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $title = $region ? ('Магазины Enter в ' . $region->getInflectedName(5)) : 'Все магазины';
        $this->setTitle($title);
        $this->setParam('title', $title);
    }

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('shop/page-region', $this->params);
    }
}