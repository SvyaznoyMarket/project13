<?php

namespace View\Main;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-main';

    public function __construct() {
        // Неправильная обертка для ajax-запроса /index/recommend
        // Для правильной обертки нужно выносить slotRecommendations() в отдельный layout более верхнего уровня
        if (\App::request()->isXmlHttpRequest()) {
            $this->engine = \App::templating();
            return;
        }
        parent::__construct();
    }

    protected function prepare() {
        $this->addMeta('viewport', 'width=960');
        $this->addMeta('mailru', 'b0645ac6fd99f8f2');

        $seo = \Model\Page\Repository::getSeo();

        if (isset($seo['title']) && !empty($seo['title'])) {
            $this->setTitle($seo['title']);
        }

        if (isset($seo['description']) && $seo['description']) {
            $this->addMeta('description', $seo['description']);
        }

        if (isset($seo['keywords']) && $seo['keywords']) {
            $this->addMeta('keywords', $seo['keywords']);
        }

        if ($this->new_menu) $this->layout = 'layout-main-new';
    }

    public function slotUserbar() {
        if ($this->new_menu) {
            return $this->render('main/_userbar');
        }
    }

    public function slotUserbarContentData() {
        return [
            'target' => '.js-showTopBar',
            'showWhenFullCartOnly' => true,
        ];
    }

    public function slotUpper() {
        if ($this->new_menu) {
            return (new \Helper\TemplateHelper())->render('common/__upper', ['offset' => '.js-showTopBar', 'showWhenFullCartOnly' => true]);
        }
    }

    public function slotBanner() {
        return $this->render('main/_banner', $this->params);
    }

    public function slotFooter() {
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
            return '';
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 22249]) . '"></div>';
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

        /**
         * @var $products               \Model\Product\Entity[]
         * @var $personalIds            int[]
         * @var $personalForWalkingIds  int[]
         */

        $return = '';
        $sender = ['name' => 'retailrocket'];

        $products = $this->getParam('productList');
        if (empty($products)) return '';
        $personalIds = @$this->getParam('rrProducts')['personal'];
        $personalForWalkingIds = @$this->getParam('rrProducts')['personal'];
        $names = [];

        // Удаление продуктов с одинаковыми именами из массива персональных рекомендаций
        array_walk ( $personalForWalkingIds , function ($id, $key) use (&$personalIds, &$names, $products) {
            // Имя продукта
            $currentProductName = trim($products[$id]->getName());
            if (array_search($currentProductName, $names) === false) {
                // Если такого имени нет в массиве имён, то добавляем имя в массив
                $names[$id] = $currentProductName;
            } else {
                // Если такое имя уже есть, то удаляем продукт из массива персональных рекомендаций
                unset($personalIds[$key]);
            }
        } );

        if (!empty($this->getParam('rrProducts')['popular'])) {
            $return .= $this->render('main/_slidesBox', [
                'blockname' => 'ПОПУЛЯРНЫЕ ТОВАРЫ',
                'class' => 'slidesBox slidesBox-items slidesBox-items-l',
                'productList' => $this->getParam('productList'),
                'rrProducts' => (array)@$this->getParam('rrProducts')['popular'],
                'sender' => $sender + ['position' => 'MainPopular', 'method' => 'ItemsToMain']
            ]);
            $return .= $this->render('main/_slidesBox', [
                'blockname' => 'МЫ РЕКОМЕНДУЕМ',
                'class' => 'slidesBox slidesBox-bg2 slidesBox-items slidesBox-items-r',
                'productList' => $this->getParam('productList'),
                'rrProducts' => (array)$personalIds,
                'sender' => $sender + ['position' => 'MainRecommended', 'method' => 'Personal']
            ]);
        }

        return $return;
    }

}
