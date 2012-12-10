<?php

namespace View;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-twoColumn';

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        $this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

        $this->addStylesheet('/css/global.css');

        $this->addJavascript('/js/jquery-1.6.4.min.js');
        $this->addJavascript('/js/LAB.min.js');
        $this->addJavascript('/js/loadjs.js');
    }

    public function slotRelLink() {
        $request = \App::request();

        $tmp = explode('?', $request->getRequestUri());
        $tmp = reset($tmp);
        $path = str_replace(array('_filter', '_tag'), '', $tmp);
        if ('/' == $path) {
            $path = '';
        }


        $relLink = $request->getSchemeAndHttpHost() . $path;

        return '<link rel="canonical" href="' . $relLink . '" />';
    }

    public function slotGoogleAnalytics() {
        return (\App::config()->googleAnalytics['enabled']) ? $this->render('_googleAnalytics') : '';
    }

    public function slotBodyDataAttribute() {
        return 'default';
    }

    public function slotBodyClassAttribute() {
        return '';
    }


    public function slotHeader() {
        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $this->getParam('rootCategories');

        if (null === $categories) {
            try {
                $categories = \RepositoryManager::getProductCategory()->getRootCollection();
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $categories = array();
            }
        }

        foreach($categories as $i => $category){
            if(!$category->getIsInMenu()){
                unset($categories[$i]);
            }
        }
        $this->setParam('rootCategories', $categories);

        return $this->render('_header', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->send('footer_default');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', array());
        }

        return $this->render('_contentHead', $this->params);
    }

    public function slotContent() {
        return '';
    }

    public function slotSidebar() {
        return '';
    }

    public function slotRegionSelection() {
        /** @var $regions \Model\Region\Entity */
        $regions = $this->getParam('shopAvailableRegions', null);

        if (null === $regions) {
            try {
                $regions = \RepositoryManager::getRegion()->getShopAvailableCollection();
            } catch (\Exception $e) {
                \App::logger()->error($e);

                $regions = array();
            }
        }

        return $this->render('_regionSelection', array_merge($this->params, array('regions' => $regions)));
    }

    public function slotInnerJavascript() {
        return $this->render('_innerJavascript');
    }

    public function slotAuth() {
        return $this->render('_auth');
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('_yandexMetrika') : '';
    }

    public function slotMetaOg() {
        return '';
    }

    public function slotAdvanceSeoCounter() {
        return '';
    }

    public function slotAdriver() {
        return '';
    }

    public function slotRootCategory() {
        return $this->render('product-category/_root', array('categories' => $this->getParam('rootCategories')));
    }

    public function slotBanner() {
        return '';
    }
}
