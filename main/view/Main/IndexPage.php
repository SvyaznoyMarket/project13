<?php

namespace View\Main;

use Model\Banner\BannerEntity;

class IndexPage extends \View\DefaultLayout {

    protected $layout  = 'layout-main-new';

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

        $seo = $this->getParam('seo');

        if (!empty($seo['title'])) {
            $this->setTitle($seo['title']);
        }

        if (!empty($seo['description'])) {
            $this->addMeta('description', $seo['description']);
        }

        if (!empty($seo['keywords'])) {
            $this->addMeta('keywords', $seo['keywords']);
        }
    }

    public function slotUserbar() {
        return $this->render('main/_userbar');
    }

    public function slotUserbarContentData() {
        return [
            'target' => '.js-showTopBar',
            'showWhenFullCartOnly' => true,
        ];
    }

    public function slotUpper() {
        return (new \Helper\TemplateHelper())->render('common/__upper', ['offset' => '.js-showTopBar', 'showWhenFullCartOnly' => true]);
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        return parent::slotGoogleRemarketingJS(['pagetype' => 'homepage']);
    }


    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return '';
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 22249]) . '"></div>';
    }

    public function slotMetaOg()
    {
        /** @var $banners BannerEntity[] */
        $banners = $this->params['banners'];
        $result = '';

        if (isset($banners[0])) {
            $imageUrl = $banners[0]->getImageBig();
            $result .=  sprintf('
    <meta property="og:image" content="%s" />
    <link rel="image_src" href="%s" />',
                $imageUrl, $imageUrl);
        }

        return $result;
    }

    public function slotRecommendations() {

        /**
         * @var $products               \Model\Product\Entity[]
         * @var $recommendations         \Model\Recommendation\RecommendationInterface[]
         */

        $return = '';

        $products = $this->getParam('productList');
        $recommendations = $this->getParam('rrProducts');
        if (empty($products) || empty($recommendations)) return '';

        if (\App::abTest()->isRichRelRecommendations()) {
            $popular = $recommendations['home_page.rr1'];
            $personal = $recommendations['home_page.rr2'];
        } else {
            $popular = $recommendations['popular'];
            $personal = $recommendations['personal'];
        }

        $sender = ['name' => $popular->getSenderName()];

        $return .= $this->render('main/_slidesBox', [
            'blockname' => $popular->getMessage(),
            'class' => 'slidesBox slidesBox-items slidesBox-items-l',
            'productList' => $products,
            'rrProducts' => $popular->getProductIds(),
            'sender' => $sender + ['position' => $popular->getPlacement(), 'method' => 'ItemsToMain'],
            'recommendationItem'    => $popular
        ]);

        $return .= $this->render('main/_slidesBox', [
            'blockname' => $personal->getMessage(),
            'class' => 'slidesBox slidesBox-bg2 slidesBox-items slidesBox-items-r',
            'productList' => $products,
            'rrProducts' => $personal->getProductIds(),
            'sender' => $sender + ['position' => $personal->getPlacement(), 'method' => 'Personal'],
            'recommendationItem'    => $personal
        ]);

        return $return;
    }

    public function slotInfoBox() {
        if ('on' === \App::request()->headers->get('SSI')) {
            return \App::helper()->render(
                '__ssi-cached',
                [
                    'path'  => '/main/category-block',
                    'query' => [
                        'regionId' => \App::user()->getRegion()->id ?: \App::config()->region['defaultId'],
                    ],
                ]
            );
        } else {
            return \App::mustache()->render('main/infoBox', [
                'categories' => array_values(array_map(function (\Model\Product\Category\Entity $category) {
                    return [
                        'name' => $category->name,
                        'url' => $category->getLink(),
                        'image' => [
                            'url' => $category->getMediaSource('category_163x163')->url,
                        ],
                    ];
                }, $this->getParam('infoBoxCategoriesByUis')))
            ]);
        }
    }

    public function slotMyThings($data) {
        return parent::slotMyThings(['Action' => '200']);
    }


}
