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

    /**
     * @param string $regionToken
     * @param string $shopToken
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show($regionToken, $shopToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();
        $shop = \RepositoryManager::shop()->getEntityByToken($shopToken);
        if (!$shop) {
            throw new \Exception\NotFoundException(sprintf('Магазин @s не найден', $shopToken));
        }

        $page = new \Mobile\View\Shop\ShowPage();
        $page->setParam('region', $region);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }
}