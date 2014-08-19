<?php

namespace View\Main;

class IndexParallaxPage extends IndexPage {
    protected $layout = 'layout-main-parallax';

    public function slotBodyDataAttribute() {
        return 'main-parallax';
    }

    public function slotBodyClassAttribute() {
        return 'parallax';
    }

    protected function prepare() {
        $this->addStylesheet('/css/game/slots/style.css');
        parent::prepare();
    }

    public function slotGameBandit() {
        return $this->render('game/page-bandit', $this->params);

//        return '
//            <div class="wrapper gameBandit">
//                <div class="content" src="" data-rimage="">' .
//                    $this->render('game/page-bandit', $this->params)
//                . '</div>
//            </div>';
    }

    /**
     * Активация виджета идентификации Enter Prize
     * @return string
     */
    public function slotEnterPrizeWidget(){
        return $this->render('enterprize/_contentRegisterAuthWidget');
    }
}