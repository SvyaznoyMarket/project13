<?php

namespace Controller\Shop;

class Action {
    /**
     * @return \Http\Response
     */
    public function index() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

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
        $shopAvailableRegions = [];
        \RepositoryManager::region()->prepareShopAvailableCollection(function($data) use (&$shopAvailableRegions) {
            $firstElements = [];
            $elements = [];
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

        // TODO: запрашиваем меню

        // магазины
        /** @var $shops \Model\Shop\Entity[] */
        $shops = [];
        \RepositoryManager::shop()->prepareCollectionByRegion(null, function($data) use (&$shops) {
            foreach ($data as $item) {
                $shops[] = new \Model\Shop\Entity($item);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $this->prepareShopsBeforeSort($shops);

        // маркеры
        $markers = [];
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
                'subway_name'       => $shop->getSubwayName(),
            );
        }

        $this->sortMarkersBySubways($markers);

        $page = new \View\Shop\RegionPage();
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
        $page->setParam('currentRegion', null);
        $page->setParam('regions', $regions);
        $page->setParam('shops', $shops);
        $page->setParam('markers', $markers);

        return new \Http\Response($page->show());
    }

    /**
     * @param int $regionId
     * @return \Http\RedirectResponse
     */
    public function region($regionId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        return new \Http\RedirectResponse(\App::router()->generate('shop'), 301);
    }

    /**
     * @param string $regionToken
     * @param string $shopToken
     * @return \Http\Response
     * @throws \Exception\AccessDeniedException
     * @throws \Exception\NotFoundException
     */
    public function show($regionToken, $shopToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

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
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        $currentRegion = $regionToken == $region->getToken() ? $region : \RepositoryManager::region()->getEntityByToken($regionToken);
        if (!$currentRegion) {
            throw new \Exception\NotFoundException(sprintf('Region @%s not found', $regionToken));
        }

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

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
            throw new \Exception\NotFoundException(sprintf('Shop @%s not found', $shopToken));
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
        $page->setParam('currentRegion', $currentRegion);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }


    /**
     * @param array $shops
     */
    private function prepareShopsBeforeSort(&$shops) {
        $makeForRegion = 14974; // Moskva

        foreach($shops as $shop) {
            /* @var $shop \Model\Shop\Entity */

            if ( $makeForRegion === $shop->getRegion()->getId() ) {
                $forReplaceArr = [
                    'Пункт выдачи,',
                    $shop->getAddress(),
                ];
                $subwayName = $shop->getName();
                $subwayName = str_replace($forReplaceArr, '', $subwayName);
                $subwayName = $this->checkSubwayName($subwayName);

                if ($subwayName) {
                    $shop->setSubwayName( $subwayName );
                }
            }
        }
    }

    /**
     * @param   string    $name
     * @return  bool|string
     */
    private function checkSubwayName($name) {
        $subwayPrefix = 'м. ';
        //print_r( mb_strpos($name, $subwayPrefix, 0, 'UTF-8') );
        if ( false === strpos($name, $subwayPrefix) ) return false;
        $name = str_replace($subwayPrefix, '', $name);
        $pos = strpos($name, ',');
        if ($pos) {
            $name = substr($name, 0, $pos);
        }
        $name = trim($name);
        if (empty($name)) return false;
        return $name;
    }


    /**
     * @param array $markers
     */
    private function sortMarkersBySubways(&$markers)
    {
        usort($markers, function($a, $b) {
            if (
                empty($a['subway_name']) &&
                empty($b['subway_name'])
            ) return 0;

            if ($a['subway_name'] == $b['subway_name']) return 0;
            return $a['subway_name'] < $b['subway_name'] ? -1 : 1;
        });
    }

}