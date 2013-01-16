<?php

namespace Controller\Shop;

class Action {
    public function index() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::$exception = null;
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }*/

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $shopAvailableRegions = array();
        \RepositoryManager::region()->prepareShopAvailableCollection(function($data) use (&$shopAvailableRegions) {
            $firstElements = array();
            $elements = array();
            foreach ($data as $item) {
                $region = new \Model\Region\Entity($item);
                //если прилетела Москва
                if (14974 == $region->getId()) {
                    //если Москва, добавляем ее в начало
                    array_unshift($firstElements, $region);
                } elseif (108136 == $region->getId()) {
                    //если Питер, добавляем его в конец
                    $firstElements[] = $region;
                } else {
                    $elements[] = $region;
                }
            }
            foreach ($firstElements as $item) {
                $shopAvailableRegions[] = $item;
            }
            foreach ($elements as $item) {
                $shopAvailableRegions[] = $item;
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        $regions = $shopAvailableRegions;

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::productCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // магазины
        /** @var $shops \Model\Shop\Entity[] */
        $shops = array();
        \RepositoryManager::shop()->prepareCollectionByRegion(null, function($data) use (&$shops) {
            foreach ($data as $item) {
                $shops[] = new \Model\Shop\Entity($item);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        // маркеры
        $markers = array();
        foreach ($shops as $shop) {
            $markers[$shop->getId()] = array(
                'id'                => $shop->getId(),
                'region_id'         => $shop->getRegion()->getId(),
                'link'              => \App::router()->generate('shop.show', array('regionToken' => $shop->getRegion()->getToken(), 'shopToken' => $shop->getToken())),
                'name'              => $shop->getName(),
                'address'           => $shop->getAddress(),
                'regtime'           => $shop->getRegime(),
                'latitude'          => $shop->getLatitude(),
                'longitude'         => $shop->getLongitude(),
                'is_reconstruction' => $shop->getIsReconstructed(),
            );
        }

        $page = new \View\Shop\RegionPage();
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('currentRegion', null);
        $page->setParam('regions', $regions);
        $page->setParam('shops', $shops);
        $page->setParam('markers', $markers);

        return new \Http\Response($page->show());
    }

    public function region($regionId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов, где есть магазины
        $shopAvailableRegions = array();
        \RepositoryManager::region()->prepareShopAvailableCollection(function($data) use (&$shopAvailableRegions) {
            foreach ($data as $item) {
                $shopAvailableRegions[] = new \Model\Region\Entity($item);
            }
        });

        // запрашиваем список регионов для выбора
        $regionsToSelect = array();
        \RepositoryManager::region()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        $currentRegion = $regionId == $region->getId() ? $region : \RepositoryManager::region()->getEntityById($regionId);
        if (!$currentRegion) {
            throw new \Exception\NotFoundException(sprintf('Region #%s not found', $regionId));
        }

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::productCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // магазины
        /** @var $shops \Model\Shop\Entity[] */
        $shops = array();
        \RepositoryManager::shop()->prepareCollectionByRegion($currentRegion, function($data) use (&$shops) {
            foreach ($data as $item) {
                $shops[] = new \Model\Shop\Entity($item);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

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
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('currentRegion', $currentRegion);
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('shops', $shops);
        $page->setParam('markers', $markers);

        return new \Http\Response($page->show());
    }

    public function show($regionToken, $shopToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = array();
        \RepositoryManager::region()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        $currentRegion = $regionToken == $region->getToken() ? $region : \RepositoryManager::region()->getEntityByToken($regionToken);
        if (!$currentRegion) {
            throw new \Exception\NotFoundException(sprintf('Region with token %s not found', $regionToken));
        }

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::productCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // магазин
        /** @var $shop \Model\Shop\Entity */
        $shop = null;
        \RepositoryManager::shop()->prepareEntityByToken($shopToken, function($data) use (&$shop) {
            $data = reset($data);
            if ((bool)$data) {
                $shop = new \Model\Shop\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$shop) {
            throw new \Exception\NotFoundException(sprintf('Shop with token %s not found', $shopToken));
        }
        // hardcode
        if (in_array($shop->getId(), array(1))) {
            $shop->setPanorama(new \Model\Shop\Panorama\Entity(array(
                'swf' => '/panoramas/shops/' . $shop->getId() . '/tour.swf',
                'xml' => '/panoramas/shops/' . $shop->getId() . '/tour.xml',
            )));
        }

        $page = new \View\Shop\ShowPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('currentRegion', $currentRegion);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }
}