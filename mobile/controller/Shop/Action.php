<?php

namespace Mobile\Controller\Shop;

class Action {
    /**
     * @return \Http\Response
     */
    public function index() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();
        $shops = \RepositoryManager::shop()->getCollectionByRegion($region);

        $page = new \Mobile\View\Shop\IndexPage();
        $page->setParam('region', $region);
        $page->setParam('shops', $shops);

        return new \Http\Response($page->show());
    }
}