<?php

namespace View\Main;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-main';

    protected function prepare() {
        $this->addMeta('viewport', 'width=960');
    }

    public function slotBanner() {
        return $this->render('main/_banner', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->send('footer_main_v2');
        } catch (\Exception $e) {
            \App::$exception = $e;
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }

    public function slotInnerJavascript() {
        return ''
            . $this->render('main/_odinkod')
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
