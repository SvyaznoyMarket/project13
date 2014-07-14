<?php

namespace View\Game;

/**
 * @author vadim.kovalenko
 */
class BanditPage extends Layout {


    public function prepare() {
        parent::prepare();
        $this->addStylesheet('/css/game/slots/style.css');
        $this->addJavascript('/js/game/slots/jquery.transit.js');
        $this->addJavascript('/js/game/slots/slots.js');
    }


    public function slotContent() {
        return $this->render('game/page-bandit', $this->params);
    }
}
