<?php

namespace View\Bandit;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-bandit';

    public function slotBodyDataAttribute() {
        return 'bandit';
    }

    public function slotBodyClassAttribute() {
        return 'bandit';
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