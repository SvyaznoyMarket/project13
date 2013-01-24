<?php

namespace Mobile\View;

class DefaultLayout extends \Templating\HtmlLayout {
    protected $layout  = 'layout-default';

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        //$this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no');
        $this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

        $this->addStylesheet('/css/mobile.css');

        //$this->addJavascript('/js/jquery-1.6.4.min.js');
    }

    public function slotHeader() {
        /** @var $regions \Model\Region\Entity */
        $regions = $this->getParam('regionsToSelect', null);

        if (null === $regions) {
            try {
                $regions = \RepositoryManager::region()->getShowInMenuCollection();
            } catch (\Exception $e) {
                \App::logger()->error($e);

                $regions = array();
            }
        }

        return $this->render('_header', array_merge($this->params, array('regions' => $regions)));
    }

    public function slotFooter() {
        return $this->render('_footer', $this->params);
    }
}
