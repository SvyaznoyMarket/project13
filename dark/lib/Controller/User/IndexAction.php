<?php

namespace Controller\User;

class IndexAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::getUser()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::getRegion()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $shopAvailableRegions = array();
        \RepositoryManager::getRegion()->prepareShopAvailableCollection(function($data) use (&$shopAvailableRegions) {
            foreach ($data as $item) {
                $shopAvailableRegions[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем количество заказов пользователя
        /** @var $product \Model\Product\Entity */
        $orderCount = 0;
        \RepositoryManager::getOrder()->prepareCollectionByUserToken(\App::user()->getToken(), function($data) use(&$orderCount) {
            $orderCount = (bool)$data ? count($data) : 0;
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $page = new \View\User\IndexPage();
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('orderCount', $orderCount);

        return new \Http\Response($page->show());
    }
}