<?php

namespace light;

class FillerLayout extends FillerObject
{
    public function run()
    {
        $wpRequest = new \WPRequest();
        $wpRequest->setUrl(WP_URL);
        $wpResponse = @$wpRequest->send('footer_default', array('shop_count' => 0));

        $renderer = App::getHtmlRenderer();
        $renderer->addParameter('wpFooter', $wpResponse['content']);

        $renderer->addCss('font.css');
        $renderer->addCss('jquery-ui-1.8.20.custom.css');
        $renderer->addCss('navy.css');
        $renderer->addCss('skin/inner.css');

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

        #$renderer->addParameter('pageTitle', 'Test page title (filled in layout filler)');

        /*$breadCrumbList = array(
            array(
                'name' => 'Главная',
                'url'  => 'http://enter.n'
            ),
            array(
                'name' => 'Второстепенная',
                'url' => 'http://enter.n'
            )
        );*/

        #$renderer->addParameter('breadCrumbList', $breadCrumbList);
    }
}