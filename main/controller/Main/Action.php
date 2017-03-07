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

        $seo = [
            'title' => null,
            'description' => null,
        ];
        $callbackPhrases = [];
        \App::scmsClient()->addQuery(
            'api/parameter/get-by-keys',
            [
                'keys' => ['title', 'description', 'site_call_phrases']
            ],
            [],
            function($data) use (&$seo, &$callbackPhrases) {
                if (!is_array($data)) {
                    return;
                }

                foreach ($data as $item) {
                    $key = $item['key'];
                    $value = $item['value'];
                    if (array_key_exists($key, $seo)) {
                        $seo[$key] = $value;
                    } else if ('site_call_phrases' === $key) {
                        $callbackPhrases = json_decode($value, true);
                        $callbackPhrases = is_array($callbackPhrases) && array_key_exists('main', $callbackPhrases) ? $callbackPhrases['main'] : [];
                    }
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

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
                        if (is_string($sliceRequestFilters['barcode'])) {
                            $sliceRequestFilters['barcode'] = explode(',', $sliceRequestFilters['barcode']);
                        }
                        $sliceRequestFilters['barcode'] = array_slice($sliceRequestFilters['barcode'], 0, $config->coreV2['chunk_size']);

                        $productListQuery = new Query\Product\GetByBarcodeList();
                        $productListQuery->barcodes = $sliceRequestFilters['barcode'];
                        $productListQuery->regionId = $region->getId();
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



        $infoBoxCategoriesByUis = [];
        if ('on' !== \App::request()->headers->get('SSI')) {
            /** @var \Model\Product\Category\Entity[] $infoBoxCategoriesByUis */
            $infoBoxCategoriesByUis = [
                '78bcec47-e1c0-4798-9cf7-1de705b348f6' => null,
                '9f47c28e-4a2a-470b-b90c-6e34d5fd311c' => null,
                'fb0b080f-11ad-495c-b684-e80ba0104237' => null,
                'abd31da8-37ba-4335-a78b-7f0d8fbb1f25' => null,
                'b9f11b13-6aae-4f1f-847b-e6c48334638b' => null,
                '7f5accb9-3d3f-495c-8a5f-40b26db31a0a' => null,
                '8c648846-e82b-4419-9a9c-777983d3a486' => null,
            ];
            \App::scmsClient()->addQuery('category/gets', [
                'uids' => array_keys($infoBoxCategoriesByUis),
                'geo_id' => $region->id,
            ], [], function ($data) use (&$infoBoxCategoriesByUis) {
                if (isset($data['categories']) && is_array($data['categories'])) {
                    foreach ($data['categories'] as $item) {
                        $category = new \Model\Product\Category\Entity($item);
                        $infoBoxCategoriesByUis[$category->ui] = $category;
                    }
                }
            });
        }

        $popularProducts = [];
        $personalProducts = [];
        call_user_func(function() use(&$popularProducts, &$personalProducts, $client) {
            $products = [];

            \App::searchClient()->addQuery('v2/listing/list', [
                'filter' => [
                    'filters' => [
                        ['is_model', 1, 1],
                        ['is_view_list', 1, 1],
                        ['label', 1, [6, 210, 213, 4, 1, 18, 21, 3, 36, 20]],
                    ],
                    'sort' => [
                        'margin_fact_relative' => 'desc',
                    ],
                    'limit' => 30,
                ],
                'region_id' => \App::user()->getRegion()->getId(),
            ], [], function($response) use(&$products) {
                if (!isset($response['list'][0])) {
                    return;
                }

                foreach ($response['list'] as $id) {
                    $products[] = new \Model\Product\Entity(['id' => $id]);
                }
            });

            $client->execute();

            if ($products) {
                \RepositoryManager::product()->prepareProductQueries($products, 'model media label brand category');
            }

            $client->execute();

            $products = array_filter($products, function($product){
                /** @var \Model\Product\Entity $product */
                return ($product instanceof \Model\Product\Entity) && $product->getIsBuyable() && !$product->isInShopShowroomOnly();
            });

            shuffle($products);
            $border = mt_rand(10, 20);
            $popularProducts = array_slice($products, 0, $border);
            $personalProducts = array_slice($products, $border);
        });

        $page = new \View\Main\IndexPage();
        $page->setParam('banners', array_values($bannersByUi));
        $page->setParam('seo', $seo);
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);
        $page->setParam('infoBoxCategoriesByUis', array_filter($infoBoxCategoriesByUis));
        $page->setParam('popularProducts', $popularProducts);
        $page->setParam('personalProducts', $personalProducts);

        return new \Http\Response($page->show());
    }
}
