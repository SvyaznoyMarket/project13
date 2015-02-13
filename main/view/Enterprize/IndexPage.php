<?php

namespace View\Enterprize;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-enterprize';

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
        return parent::slotBodyClassAttribute() . ' enterprize';
    }

    public function slotContentHead() {
        return parent::slotContentHead() . $this->render('enterprize/_auth');
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
        if ((bool)$this->getParam('isRegistration')) {
            $flocktoryData = [
                'user' => [
                    'name' => null,
                    'email' => null,
                    'sex' => null,
                ],
                'order' => [
                    'id' => uniqid(),
                    'price' => 2000,
                    'custom_field' => 'my_custom_id',
                    'items' => [
                        ['id' => 777, 'title' => 'Nike Shoes', 'price' => 1000, 'image' => 'http://path.to.image', 'count' => 1]
                    ]
                ],
                'spot' => 'some_spot',
            ];

            $return .= '<div id="flocktoryEnterprizeRegJS" class="jsanalytics" data-value="' . $this->json($flocktoryData) . '"></div>';
        }

        // Enterprize registration analytics
        if ((bool)$this->getParam('isRegistration')) {
            $return .= '<div id="enterprizeRegAnalyticsJS" class="jsanalytics"></div>';
        }

        return $return;
    }
}
