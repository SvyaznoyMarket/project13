<?php

namespace View;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-twoColumn';

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', 'enter');
        $this->addMeta('viewport', 'width=900');
        $this->addMeta('title', 'Enter');
        $this->addMeta('description', 'Enter');

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
        if (!$this->hasParam('rootCategories')) {
            $rootCategories = \RepositoryManager::getProductCategory()->getRootCollection();
            foreach($rootCategories as $i => $category){
                if(!$category->getIsInMenu()){
                    unset($rootCategories[$i]);
                }
            }
            $this->setParam('rootCategories', $rootCategories);
        }

        return $this->render('_header', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->send('footer_default', array('shop_count' => \App::coreClientV2()->query('shop/get-quantity')));
        } catch (\Exception $e) {
            \App::$exception = $e;
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
        return $this->render('_regionSelection');
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
}
