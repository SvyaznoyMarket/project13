<?php

namespace View\Main;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-main';

    public function __construct() {
        if (\App::request()->isXmlHttpRequest()) {
            $this->engine = \App::templating();
            return;
        }
        parent::__construct();
    }

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

        if ($this->new_menu) $this->layout = 'layout-main-new';
    }

    public function slotBanner() {
        return $this->render('main/_banner', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        /*
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
        */

        return (new \Helper\TemplateHelper())->render('main/__footer');
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

    public function slotMetaOg()
    {
        $result = '';

        if (isset($this->params['bannerData'][0]['imgb'])) {
            $image_url = $this->params['bannerData'][0]['imgb'];
            $result .=  "<meta property=\"og:image\" content=\"" . $image_url . "\" />\r\n".
                        "<link rel=\"image_src\" href=\"". $image_url . "\" />\r\n";
        }

        return $result;
    }

    public function slotRecommendations() {

        $return = '';

        if (!empty(@$this->getParam('rrProducts')['popular'])) {
            $return .= $this->render('main/_slidesBox', ['class' => 'slidesBox slidesBox-items fl-l', 'productList' => $this->getParam('productList'), 'rrProducts' => (array)@$this->getParam('rrProducts')['popular']]);
            $return .= $this->render('main/_slidesBox', ['class' => 'slidesBox slidesBox-bg2 slidesBox-items fl-r', 'productList' => $this->getParam('productList'), 'rrProducts' => (array)@$this->getParam('rrProducts')['personal']]);
        }

        return $return;
    }

}
