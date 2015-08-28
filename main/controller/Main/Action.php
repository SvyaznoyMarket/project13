<?php

namespace Controller\Main;

use EnterApplication\CurlTrait;
use Model\Banner\BannerEntity;
use EnterQuery as Query;

class Action {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function index(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $config = \App::config();
        $region = \App::user()->getRegion();

        // запрашиваем баннеры
        /** @var \Model\Banner\BannerEntity[] $bannersByUi */
        $bannersByUi = [];
        \RepositoryManager::banner()->prepareCollection(function ($data) use (&$bannersByUi, &$itemsByBanner) {
            foreach ($data as $item) {
                if (!@$item['uid']) continue;
                $banner = new BannerEntity($item);
                $bannersByUi[$banner->uid] = $banner;
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        // проверка доступности баннеров в регионе
        try {
            if (($config->banner['checkStatus']) && ($config->region['defaultId'] !== $region->getId())) {
                /** @var Query\Product\GetUiPager[]|Query\Product\GetByUiList[] $productCheckQueriesByBannerUi */
                $productCheckQueriesByBannerUi = [];
                foreach ($bannersByUi as $banner) {
                    if (!$slice = $banner->slice) {
                        continue;
                    }

                    $sliceRequestFilters = [];
                    parse_str($slice->getFilterQuery(), $sliceRequestFilters);
                    if ((1 === count($sliceRequestFilters)) && !empty($sliceRequestFilters['barcode'])) {
                        $sliceRequestFilters['barcode'] = '2200602000363';
                        if (is_string($sliceRequestFilters['barcode'])) {
                            $sliceRequestFilters['barcode'] = explode(',', $sliceRequestFilters['barcode']);
                        }
                        $sliceRequestFilters['barcode'] = array_slice($sliceRequestFilters['barcode'], 0, $config->coreV2['chunk_size']);

                        $productListQuery = new Query\Product\GetByUiList();
                        $productListQuery->uis = $sliceRequestFilters['barcode'];
                        $productListQuery->regionId = $region->getId();
                        $productListQuery->filter->model = false;
                        $productListQuery->prepare();
                        $productCheckQueriesByBannerUi[$banner->uid] = $productListQuery;
                    } else {
                        $productUiPagerQuery = new Query\Product\GetUiPager();
                        $productUiPagerQuery->regionId = $region->getId();
                        $productUiPagerQuery->filter->data = \RepositoryManager::slice()->getSliceFiltersForSearchClientRequest($slice);
                        $productUiPagerQuery->offset = 0;
                        $productUiPagerQuery->limit = 1;
                        $productUiPagerQuery->prepare();
                        $productCheckQueriesByBannerUi[$banner->uid] = $productUiPagerQuery;
                    }
                }

                $this->getCurl()->execute();

                foreach ($productCheckQueriesByBannerUi as $bannerUi => $productCheckQuery) {
                    if (
                        !$productCheckQuery->error
                        && ($banner = $bannersByUi[$bannerUi])
                        && (
                            (($productCheckQuery instanceof Query\Product\GetUiPager) && !count($productCheckQuery->response->uids))
                            || (($productCheckQuery instanceof Query\Product\GetByUiList) && !count($productCheckQuery->response->products))
                        )
                    ) {
                        unset($bannersByUi[$bannerUi]);
                        \App::logger()->info(['message' => 'Баннер уничтожен', 'banner.ui' => $bannerUi, 'region.id' => $region->getId(), 'sender' => __FILE__ . ' ' . __LINE__]);
                    }
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' . __LINE__], ['main', 'banner']);
        }

        // товары, услуги, категории
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];

        $productsIdsFromRR = $this->getProductIdsFromRR($request);
        foreach ($productsIdsFromRR as $arr) {
            foreach ($arr as $key => $val) {
                $productsById[(int)$val] = new \Model\Product\Entity(['id' => (int)$val]);
            }
        }
        unset($val, $key, $arr);

        $productsById = array_filter($productsById);

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media label');

        $client->execute();

        $page = new \View\Main\IndexPage();
        $page->setParam('banners', array_values($bannersByUi));
        $page->setParam('productList', $productsById);
        $page->setParam('rrProducts', isset($productsIdsFromRR) ? $productsIdsFromRR : []);

        return new \Http\Response($page->show());
    }

    /** Рендер рекомендаций через ajax-запрос
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function recommendations(\Http\Request $request) {
        $rrProductIds = [];
        $productsById = [];

        // получаем продукты из RR
        $rrProducts = $this->getProductIdsFromRR($request, 1);
        foreach ($rrProducts as $collection) {
            $rrProductIds = array_merge($rrProductIds, $collection);
        }

        foreach (array_unique($rrProductIds) as $productId) {
            $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media');

        $page = new \View\Main\IndexPage();
        $page->setParam('productList', $productsById);
        $page->setParam('rrProducts', isset($rrProducts) ? $rrProducts : []);
        return new \Http\JsonResponse(['result' => $page->slotRecommendations()]);
    }

    /** Возвращает массив рекомендаций (ids)
     * @param \Http\Request $request
     * @param float $timeout Таймаут для запроса к RR
     * @return array
     */
    public function getProductIdsFromRR(\Http\Request $request, $timeout = 1.15) {
        $rrClient = \App::rrClient();
        $rrUserId = $request->cookies->get('rrpusid');
        $ids = [
            'popular' => [],
            'personal' => []
        ];

        $rrClient->addQuery(
            'ItemsToMain',
            [],
            [],
            function($data) use (&$ids) {
                $ids['popular'] = (array)$data;
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                \App::exception()->remove($e);
            },
            $timeout
        );
        if ($rrUserId) {
            $rrClient->addQuery(
                'PersonalRecommendation',
                ['rrUserId' => $rrUserId],
                [],
                function($data) use (&$ids) {
                    $ids['personal'] = (array)$data;
                },
                function(\Exception $e) {
                    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                    \App::exception()->remove($e);
                },
                $timeout
            );
        }

        $rrClient->execute();


        // если нет персональных рекомендаций, то выдадим половину популярных за персональные
        if (empty($ids['personal']) && !empty($ids['popular'])) {
            foreach ($ids['popular'] as $key => $item) {
                if ($key % 2) {
                    $ids['personal'][] = $item;
                    unset($ids['popular'][$key]);
                }
            }
        }

        return $ids;

    }
}
