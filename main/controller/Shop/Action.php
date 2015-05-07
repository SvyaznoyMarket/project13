<?php

namespace Controller\Shop;

class Action {
    /**
     * @return \Http\Response
     */
    public function index() {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $helper = new \Helper\TemplateHelper();

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

        $regions = $shopAvailableRegions;

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // магазины
        /** @var $shops \Model\Shop\Entity[] */
        $shops = [];
        \App::scmsClient()->addQuery('shop/get', [], [], function($data) use (&$shops) {
            foreach ($data as $item) {
                $shop = new \Model\Shop\Entity($item);
                if (!$shop->getLatitude() || !$shop->getLongitude()) continue;

                $shops[] = $shop;
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $this->prepareShopsBeforeSort($shops);

        // маркеры
        $markers = [];
        foreach ($shops as $shop) {
            if ($shop->getRegion() && in_array($shop->getId(), [194])) {
                \RepositoryManager::product()->prepareIteratorByFilter(
                    [
                    ['shop', 1, [$shop->getId()]],
                    ['is_view_list', 1, [true]],
                    ],
                    [],
                    null,
                    null,
                    new \Model\Region\Entity(['id' => $shop->getRegion()->getId()]),
                    function($data) use (&$shop) {
                        $shop->setProductCount(isset($data['count']) ? $data['count'] : null);
                    },
                    function(\Exception $e) {
                        \App::exception()->remove($e);
                    }
                );
                \App::coreClientV2()->execute();
            }

            $subways = $shop->getSubway();
            $subway = isset($subways[0]) ? $subways[0] : null;

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
                'subway_name'       => $subway ? $subway->getName() : null,
                'subway_color'      => ($subway && $subway->getLine()) ? $subway->getLine()->getColor() : null,
                'product_count_text' => $shop->getProductCount() ? ($shop->getProductCount() . ' ' .$helper->numberChoice($shop->getProductCount(), ['товар', 'товара', 'товаров']) . ' можно забрать сегодня') : null,
            );
        }

        $markers = array_values($markers);

        $page = new \View\Shop\RegionPage();
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
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
        //\App::logger()->debug('Exec ' . __METHOD__);

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
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        $regionConfig = [];
        if ($user->getRegionId()) {
            $regionConfig = (array)\App::dataStoreClient()->query("/region/{$user->getRegionId()}.json");

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // выполнение 1-го пакета запросов
        $client->execute();

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // магазин
        /** @var $shop \Model\Shop\Entity */
        $shop = null;
        \App::scmsClient()->addQuery('shop/get', ['slug' => [$shopToken]], [], function($data) use (&$shop) {
            if (isset($data[0]['name'])) {
                $shop = new \Model\Shop\Entity($data[0]);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$shop) {
            throw new \Exception\NotFoundException(sprintf('Shop @%s not found', $shopToken));
        }

        // hardcode
        if (in_array($shop->getId(), [1])) {
            $shop->setPanorama(new \Model\Shop\Panorama\Entity([
                'swf' => '/panoramas/shops/' . $shop->getId() . '/tour.swf',
                'xml' => '/panoramas/shops/' . $shop->getId() . '/tour.xml',
            ]));
        }

        if (in_array($shop->getId(), [194])) {
            \RepositoryManager::product()->prepareIteratorByFilter(
                [
                    ['shop', 1, [$shop->getId()]],
                    ['is_view_list', 1, [true]],
                ],
                [],
                null,
                null,
                $shop->getRegion(),
                function($data) use (&$shop) {
                    $shop->setProductCount(isset($data['count']) ? $data['count'] : null);
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );

        }
        \App::curl()->execute();

        $page = new \View\Shop\ShowPage();
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
}