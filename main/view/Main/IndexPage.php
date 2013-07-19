<?php

namespace View\Main;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-main';

    protected function prepare() {
        $this->addMeta('viewport', 'width=960');
        $this->addMeta('mailru', 'b0645ac6fd99f8f2');

        // Seo-теги загружаем из json (для главной страницы, например)
        if ( $this->hasParam('seoPage') ) {
            $seoPage = $this->getParam('seoPage');
            if ( isset($seoPage['title']) and !empty($seoPage['title']) ) $this->setTitle( $seoPage['title'] );

            if ( isset($seoPage['metas']) and is_array($seoPage['metas']) )
            foreach( $seoPage['metas'] as $key => $val){
                $this->addMeta($key, $val);
            }
        }
    }

    public function slotBanner() {
        return $this->render('main/_banner', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = (14974 == \App::user()->getRegion()->getId() || 83 == \App::user()->getRegion()->getParentId()) ? $client->query('footer_main_moscow') : $client->query('footer_main_v2');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }

    public function slotInnerJavascript() {
        return ''
            . "\n\n"
            . $this->render('_remarketingGoogle', ['tag_params' => ['pagetype' => 'homepage']])
            . "\n\n"
            . $this->render('_innerJavascript');
    }
}
