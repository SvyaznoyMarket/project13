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

    public function slotMicroformats() {
        return
            parent::slotMicroformats() .
            '<script type="application/ld+json">' . json_encode(call_user_func(function() {
                $url = \App::request()->getScheme() . '://' . \App::config()->mainHost;
                return [
                    '@context' => 'http://schema.org',
                    '@type' => 'WebSite',
                    'url' => $url . $this->url('homepage'),
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => $url . $this->url('search') . '?q={search_term}',
                        'query-input' => 'required name=search_term',
                    ],
                ];
            }), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"
            ;
    }
    
    public function slotRecommendations() {
        /** @var $popularProducts \Model\Product\Entity[] */
        $popularProducts = $this->getParam('popularProducts');
        /** @var $personalProducts \Model\Product\Entity[] */
        $personalProducts = $this->getParam('personalProducts');

        return
            $this->render('main/_slidesBox', [
                'name' => 'ПОПУЛЯРНЫЕ ТОВАРЫ',
                'class' => 'slidesBox slidesBox-items slidesBox-items-l',
                'products' => $popularProducts,
                'sender' => ['name' => 'enter', 'position' => 'main', 'method' => 'ItemsToMain'],
            ]) .
            $this->render('main/_slidesBox', [
                'name' => 'МЫ РЕКОМЕНДУЕМ',
                'class' => 'slidesBox slidesBox-bg2 slidesBox-items slidesBox-items-r',
                'products' => $personalProducts,
                'sender' => ['name' => 'enter', 'position' => 'main', 'method' => 'Personal'],
            ])
        ;
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

    public function slotAdmitadJS() {
        if (!\App::config()->partners['admitad']['enabled']) {
            return '';
        }

        return '<div id="admitadJS" class="jsanalytics" data-vars="' . $this->json([
            'type' => 'main',
        ]) . '"></div>';
    }

    public function slotGdeSlonJS() {
        return '<script async="true" type="text/javascript" src="https://www.gdeslon.ru/landing.js?mode=main&mid=81901"></script>';
    }
}
