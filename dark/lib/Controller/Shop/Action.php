<?php

namespace Controller\Shop;

class Action {
    public function index() {
        $region = \App::user()->getRegion();
    }

    public function region($regionId) {
        $currentRegion = \RepositoryManager::getRegion()->getEntityById($regionId);
        if (!$currentRegion) {
            throw new \Exception\NotFoundException(sprintf('Region #%s not found', $regionId));
        }

        //города присутствия
        $regions = \RepositoryManager::getRegion()->getShopAvailableCollection();

        // магазины
        $shops = \RepositoryManager::getShop()->getCollectionByRegion($currentRegion);
        // маркеры
        $markers = array();
        foreach ($shops as $shop) {
            $markers[$shop->getId()] = array(
                'id'                => $shop->getId(),
                'region_id'         => $currentRegion->getId(),
                'link'              => \App::router()->generate('shop.show', array('regionToken' => $currentRegion->getToken(), 'shopToken' => $shop->getToken())),
                'name'              => $shop->getName(),
                'address'           => $shop->getAddress(),
                'regtime'           => $shop->getRegime(),
                'latitude'          => $shop->getLatitude(),
                'longitude'         => $shop->getLongitude(),
                'is_reconstruction' => $shop->getIsReconstructed(),
            );
        }

        $page = new \View\Shop\RegionPage();
        $page->setParam('currentRegion', $currentRegion);
        $page->setParam('regions', $regions);
        $page->setParam('shops', $shops);
        $page->setParam('markers', $markers);

        return new \Http\Response($page->show());
    }

    public function show($regionToken, $shopToken) {
        $currentRegion = \RepositoryManager::getRegion()->getEntityByToken($regionToken);
        if (!$currentRegion) {
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
        $page->setParam('currentRegion', $currentRegion);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }
}