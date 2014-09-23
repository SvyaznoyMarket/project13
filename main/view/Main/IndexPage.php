<?php

namespace View\Main;

use View\Menu;

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

        $response = null;
        $client->addQuery(
            (14974 == \App::user()->getRegion()->getId() || 83 == \App::user()->getRegion()->getParentId())
                ? 'footer_main_moscow'
                : 'footer_main_v2'
            ,
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

        $response['content'] = str_replace('8 (800) 700-00-09', \App::config()->company['phone'], $response['content']);

        return $response['content'];
    }

    public function slotInnerJavascript() {
        return ''
            . "\n\n"
            . $this->render('_remarketingGoogle', ['tag_params' => ['pagetype' => 'homepage']])
            . "\n\n"
            . $this->render('_innerJavascript');
    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 22249]) . '"></div>';
    }

    public function slotRuTargetHomepageJS() {
        if (!\App::config()->partners['RuTarget']['enabled']) return;

        return "<div id=\"RuTargetHomepageJS\" class=\"jsanalytics\" data-value=\"" . $this->json(['regionId' => \App::user()->getRegionId()]) . "\"></div>";
    }

    public function slotMailRu() {
        return $this->render('_mailRu', [
            'pageType' => 'home',
            'productIds' => [],
            'price' => '',
        ]);
    }
}
