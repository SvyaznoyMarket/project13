<?php

namespace View\Bandit;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-bandit';

    public function slotBodyDataAttribute() {
        return 'bandit';
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' bandit';
    }

    protected function prepare() {
        $this->addStylesheet('/styles/game/slots/style.css');
        parent::prepare();
    }

    public function slotGameBandit() {
        return $this->render('game/page-bandit', $this->params);
    }

    /**
     * Активация виджета идентификации Enter Prize
     * @return string
     */
    public function slotEnterPrizeWidget(){
        return $this->render('enterprize/_contentRegisterAuthWidget');
    }
}