<?php

namespace View\Game;

/**
 * @author vadim.kovalenko
 */
class BanditPage extends Layout {

    protected $layout = 'layout-black';

    public function prepare() {
        $this->addStylesheet('/css/game/slots/style.css');
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


    /**
     * Ядрить колотить, переопределяем футер
     * @return mixed
     */
    public function slotFooter() {
        $client = \App::contentClient();

        $response = null;
        $client->addQuery(
            'footer_main_v2',
            [],
            function($data) use (&$response) {
                $response = $data;
            },
            function(\Exception $e) {
                \App::exception()->add($e);
            }
        );
        $client->execute();
        $response = array_merge(['content' => ''], (array)$response);
        return str_replace('8 (800) 700-00-09', \App::config()->company['phone'], $response['content']);
    }
}
