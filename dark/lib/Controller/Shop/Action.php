<?php

namespace Controller\Shop;

class Action {
    public function index() {
        $region = \App::user()->getRegion();
    }

    public function region($regionToken) {
        $region = \RepositoryManager::getRegion()->getEntityByToken($regionToken);
        if (!$region) {
            throw new \Exception\NotFoundException(sprintf('Region with token %s not found', $regionToken));
        }

        $shops = \RepositoryManager::getShop()->getCollectionByRegion($region);

        $page = new \View\Shop\RegionPage();
        $page->setParam('region', $region);
        $page->setParam('shops', $shops);

        return new \Http\Response($page->show());
    }

    public function show($regionToken, $shopToken) {
        $region = \RepositoryManager::getRegion()->getEntityByToken($regionToken);
        if (!$region) {
            throw new \Exception\NotFoundException(sprintf('Region with token %s not found', $regionToken));
        }

        $shop = \RepositoryManager::getShop()->getEntityByToken($shopToken);
        if (!$shop) {
            throw new \Exception\NotFoundException(sprintf('Shop with token %s not found', $shopToken));
        }
        $shop->setPanorama(new \Model\Shop\Panorama\Entity(array(
            'swf' => '/panoramas/shops/' . $shop->getId() . '/tour.swf',
            'xml' => '/panoramas/shops/' . $shop->getId() . '/tour.xml',
        )));

        $page = new \View\Shop\ShowPage();
        $page->setParam('region', $region);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }
}