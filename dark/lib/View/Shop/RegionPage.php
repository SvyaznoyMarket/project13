<?php

namespace View\Shop;

class RegionPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $region \Model\Region\Entity */
        $region = $this->getParam('region') instanceof \Model\Region\Entity ? $this->getParam('region') : null;
        if (!$region) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Магазины Enter в  ' . $region->getInflectedName(5),
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $title = 'Магазины Enter в ' . $region->getInflectedName(5);
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