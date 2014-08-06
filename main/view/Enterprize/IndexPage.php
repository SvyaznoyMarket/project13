<?php

namespace View\Enterprize;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        $return = $this->render('enterprize/page-index', $this->params);

        if ((bool)$this->getParam('hasFlocktoryPopup') || (bool)$this->getParam('isRegistration')) {
            $return .= '<div id="flocktoryEnterprizeFormJS" class="jsanalytics"></div>';
        }

//        if ((bool)$this->getParam('isRegistration')) {
//            $return .= '<div id="flocktoryAddScript" class="jsanalytics"></div>';
//        }

        return $return;
    }

    public function slotBodyClassAttribute() {
        return 'enterprize';
    }

    public function slotContentHead() {
        return parent::slotContentHead() . $this->render('enterprize/_auth');
    }

    public function slotUserbarEnterprize() {
        return '';
    }

    public function slotUserbarEnterprizeContent() {
        return '';
    }

    public function slotFlocktoryEnterprizeJs() {
        $return = '';
        if ((bool)$this->getParam('hasFlocktoryPopup')) {
            $return .= '<div id="flocktoryEnterprizeJS" class="jsanalytics"></div>';
        }

        return $return;
    }

    public function slotEnterprizeRegJS() {
        $return = '';

        // flocktory
        if ((bool)$this->getParam('isRegistration') ) {
            $flocktoryData = [
                'order_id'     => uniqid(),
                'email'        => null,
                'name'         => null,
                'sex'          => null,
                'price'        => 2000,
                'custom_field' => 'my_custom_id',
                'items'        => [
                    ['id' => 777, 'title' => 'Nike Shoes', 'price' => 1000, 'image' => 'http://path.to.image', 'count' => 1]
                ],
            ];

            $return .= '<div id="flocktoryEnterprizeRegJS" class="jsanalytics" data-value="' . $this->json($flocktoryData) . '"></div>';
        }

        return $return;
    }
}
