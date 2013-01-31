<?php

namespace View\Service;

class ShowPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        /** @var $service \Model\Product\Service\Entity */
        $service = $this->getParam('service') instanceof \Model\Product\Service\Entity ? $this->getParam('service') : null;
        if (!$service) {
            return;
        }

        /** @var $category \Model\Product\Service\Category\Entity */
        $categories = $service->getCategory();
        $category = isset($categories[2]) ? $categories[2] : null;

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Услуги F1',
                'url'  => $this->url('service'),
            );
            if ($category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => $this->url('service.category', array('categoryToken' => $category->getToken())),
                );
            }
            $breadcrumbs[] = array(
                'name' => $service->getName(),
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: title
        $this->setTitle('F1 - ' . $service->getName() . ' - Enter.ru');
        $this->setParam('title', $service->getName());
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('service/page-show', $this->params);
    }
}