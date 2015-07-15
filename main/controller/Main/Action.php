<?php

namespace Controller\Main;

use Session\AbTest\AbTest;

class Action {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function index(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        // подготовка 1-го пакета запросов

        // запрашиваем баннеры
        $itemsByBanner = [];
        $bannerData = [];
        \RepositoryManager::banner()->prepareCollection($region, function ($data) use (&$bannerData, &$itemsByBanner) {
            $timeout = \App::config()->banner['timeout'];
            $hosts = \App::config()->mediaHost;
            $host = reset($hosts);
            $urls = \App::config()->banner['url'];

            // Фильтруем баннеры для новой и старой главной
            $data = array_filter($data, function($item) {
                return 3 == (int)@$item['type_id'];
            });

            foreach ($data as $i => $item) {
                $bannerId = isset($item['id']) ? (int)$item['id'] : null;
                $item = [
                    'id'    => $bannerId,
                    'name'  => isset($item['name']) ? (string)$item['name'] : null,
                    'url'   => isset($item['url']) ? (string)$item['url'] : null,
                    'image' => isset($item['media_image']) ? (string)$item['media_image'] : null,
                    'item'  => isset($item['item_list']) ? (array)$item['item_list'] : [],
                ];

                if (empty($item['image'])) continue;

                $bannerData[] = [
                    'id'    => $bannerId,
                    'alt'   => $item['name'],
                    'imgs'  => $item['image'] ? ($host . $urls[4] . $item['image']) : null,
                    'imgb'  => $item['image'] ? ($host . $urls[3] . $item['image']) : null,
                    'url'   => $item['url'],
                    't'     => $timeout,
                    'ga'    => $bannerId . ' - ' . $item['name'],
                    'pos'   => $i,
                ];

                $itemsByBanner[$bannerId] = [];
                foreach ($item['item'] as $itemData) {
                    $itemsByBanner[$bannerId][] = new \Model\Banner\Item\Entity($itemData);
                }
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        // товары, услуги, категории
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        $medias = [];
        /** @var $categoriesById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        foreach ($itemsByBanner as $items) {
            foreach ($items as $item) {
                /** @var $item \Model\Banner\Item\Entity */
                if ($item->getProductId()) $productsById[$item->getProductId()] = null;
                if ($item->getProductCategoryId()) $categoriesById[$item->getProductCategoryId()] = null;
            }
        }

        $productsIdsFromRR = $this->getProductIdsFromRR($request);
        foreach ($productsIdsFromRR as $arr) {
            foreach ($arr as $key => $val) {
                $productsById[(int)$val] = null;
            }
        }
        unset($val, $key, $arr);

        // подготовка 2-го пакета запросов
        // запрашиваем товары
        if ((bool)$productsById) {
            \RepositoryManager::product()->useV3()->withoutModels()->prepareCollectionById(array_keys($productsById), $region, function($data) use (&$productsById) {
                if (!is_array($data)) return;

                foreach ($data as $item) {
                    $productsById[(int)$item['id']] = new \Model\Product\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить товары для баннеров');
            });

            \RepositoryManager::product()->prepareProductsMediasByIds(array_keys($productsById), $medias);
        }

        // запрашиваем категории товаров
        if ((bool)$categoriesById) {
            \RepositoryManager::productCategory()->prepareCollectionById(array_keys($categoriesById), $region, function($data) use (&$categoriesById) {
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if ($item && is_array($item)) {
                            $category = new \Model\Product\Category\Entity($item);
                            $categoriesById[$category->getId()] = $category;
                        }
                    }
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить категории товаров для баннеров');
            });
        }

        if ((bool)$productsById || (bool)$categoriesById) {
            // выполнение 2-го пакета запросов
            $client->execute();

            \RepositoryManager::product()->enrichProductsFromScms($productsById, 'media label');
            $client->execute();
        }

        // формируем ссылки для баннеров
        foreach ($bannerData as &$item) {
            $url = $item['url'];

            $bannerItems = isset($itemsByBanner[$item['id']]) ? (array)$itemsByBanner[$item['id']] : [];
            if ((bool)$bannerItems) {
                /** @var $bannerItem \Model\Banner\Item\Entity */
                $bannerItem = reset($bannerItems);
                if (!$bannerItem) continue;

                if ($bannerItem->getProductId()) {
                    $products = [];
                    foreach ($bannerItems as $bannerItem) {
                        $product = ($bannerItem->getProductId() && isset($productsById[$bannerItem->getProductId()]))
                            ? $productsById[$bannerItem->getProductId()]
                            : null;
                        if (!$product) continue;

                        $products[] = $product;
                    }
                    if (!(bool)$products) continue;

                    if (1 == count($products)) {
                        /** @var $product \Model\Product\Entity */
                        $product = reset($products);
                        $url = $product->getLink();
                    } else {
                        $barcodes = array_map(function ($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $products);
                        $url = $router->generate('product.set', [
                            'productBarcodes' => implode(',', $barcodes),
                        ]);
                    }
                } else if ($bannerItem->getProductCategoryId()) {
                    $category = isset($categoriesById[$bannerItem->getProductCategoryId()]) ? $categoriesById[$bannerItem->getProductCategoryId()] : null;
                    if (!$category instanceof \Model\Product\Category\Entity) {
                        \App::logger()->error(sprintf('Категория #%s не найдена', $bannerItem->getProductCategoryId()));
                        continue;
                    }

                    $url = $category->getLink();
                }
            }

            $item['url'] = $url;
        } if (isset($item)) unset($item);

        $bannerData = array_values($bannerData);

        $page = new \View\Main\IndexPage();
        $page->setParam('bannerData', $bannerData);
        $page->setParam('productList', $productsById);
        $page->setParam('rrProducts', isset($productsIdsFromRR) ? $productsIdsFromRR : []);

        return new \Http\Response($page->show());
    }

    /** Рендер рекомендаций через ajax-запрос
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function recommendations(\Http\Request $request) {
        $rrProductsById = [];
        $productsById = [];

        // получаем продукты из RR
        $rrProducts = $this->getProductIdsFromRR($request, 1);
        foreach ($rrProducts as $collection) {
            $rrProductsById = array_merge($rrProductsById, $collection);
        }


        // получаем продукты из ядра
        $products = \RepositoryManager::product()->useV3()->withoutModels()->getCollectionById(array_unique($rrProductsById), null, false);
        foreach ($products as $product) {
            $productsById[$product->getId()] = $product;
        }

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
    public function getProductIdsFromRR(\Http\Request $request, $timeout = 0.15) {
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
