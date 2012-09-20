<?php

namespace light;

class FillerLayout implements IFiller
{
    public function run()
    {
        $wpRequest = new \WPRequest();
        $wpRequest->setUrl(Config::get('wpUrl'));
        $wpResponse = @$wpRequest->send('footer_default', array('shop_count' => App::getCoreV2()->query('shop/get-quantity')));

        $renderer = App::getHtmlRenderer();
        $renderer->addParameter('wpFooter', $wpResponse['content']);

        $renderer->addCss('font.css');
        $renderer->addCss('jquery-ui-1.8.20.custom.css');
        $renderer->addCss('navy.css');
        $renderer->addCss('skin/inner.css');

        $renderer->addJS('jquery-1.6.4.min.js');

        $renderer->addParameter('_template', 'product_catalog');

        $ar = explode('?', $_SERVER['REQUEST_URI']);
        $path = str_replace(array('_filter', '_tag'), '', $ar[0]);
        if ($path == "/") {
            $path = '';
        }

        $renderer->addParameter('show_link', True);
        $renderer->addParameter('rel_href', 'http://' . $_SERVER['SERVER_NAME'] . $path);



        $list = App::getCategory()->getRootCategoryList();
        foreach($list as $key => $category){
            if(!$category->getIsShownInMenu()){
                unset($list[$key]);
            }
        }

        $renderer->addParameter('categoryRootList', $list, true);

        $regionTopList = App::getRegion()->getShopAvailable();
        $renderer->addParameter('regionTopList', $regionTopList);

        if(Config::getEnvironment() == 'dev' && isset($_COOKIE['debug']) && $_COOKIE['code'] == 'site')
        {
            $renderer->addParameter('debug', True);
        }
    }
}