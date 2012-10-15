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

        $renderer->addCss('global.css');

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

        $num_row = 3;

        $columns_count = array();
        $count = count($regionTopList);

        for ($i = 0; $i < $num_row; $i++)
        {
            $columns_count[$i] = (int)floor($count / $num_row) + (($count % $num_row) > $i ? 1 : 0);
        }
        $renderer->addParameter('columns_count', $columns_count);

        if(Config::isDebugMode())
        {
            $renderer->addParameter('debug', true);
        }
        else{
              $renderer->addParameter('debug', false);
        }
    }
}