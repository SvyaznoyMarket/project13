<?php

namespace View\Game;

/**
 * @author vadim.kovalenko
 */
class BanditPage extends Layout {

    protected $layout = 'layout-black';

    public function prepare() {
        $this->addStylesheet('/styles/game/slots/style.css');
//        $this->addJavascript('/js/game/slots.js');
//        $this->addJavascript('/js/prod/enterprize.js');
        parent::prepare();
    }

    public function slotBodyDataAttribute() {
        return 'slots';
    }


    /**
     * Активация виджета идентификации Enter Prize
     * @return string
     */
    public function slotEnterPrizeWidget(){
        return $this->render('enterprize/_contentRegisterAuthWidget');
    }


    public function slotContent(){
        return $this->render('game/page-bandit',$this->params);
    }


    public function slotHeader() {
        $this->params['scheme'] = 'homepage';
        return parent::slotHeader();
    }
}
