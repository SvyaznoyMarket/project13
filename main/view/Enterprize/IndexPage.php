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
        return $this->render('enterprize/page-index', $this->params);
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' enterprize';
    }

    public function slotContentHead() {
        return parent::slotContentHead() . $this->render('enterprize/_auth');
    }

    public function slotAuth() {
        return $this->render('_auth', ['oauthEnabled' => \App::config()->oauthEnabled, 'showRegisterForm' => false]);
    }

    public function slotEnterprizeRegJS() {
        $return = '';

        // Enterprize registration analytics
        if ((bool)$this->getParam('isRegistration')) {
            $return .= '<div id="enterprizeRegAnalyticsJS" class="jsanalytics"></div>';
        }

        return $return;
    }
}
