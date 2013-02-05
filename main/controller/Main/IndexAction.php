<?php

namespace Controller\Main;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        // подготовка 1-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = [];
        \RepositoryManager::productCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем баннеры
        $itemsByBanner = [];
        $bannerData = [];
        \RepositoryManager::banner()->prepareCollection($region, function ($data) use (&$bannerData, &$itemsByBanner) {
            $timeout = \App::config()->banner['timeout'];
            $urls = \App::config()->banner['url'];

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
                    'imgs'  => $item['image'] ? ($urls[0] . $item['image']) : null,
                    'imgb'  => $item['image'] ? ($urls[1] . $item['image']) : null,
                    'url'   => $item['url'],
                    't'     => $i > 0 ? $timeout : $timeout + 4000,
                    'ga'    => $bannerId . ' - ' . $item['name'],
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
        /** @var $productsById \Model\Product\BasicEntity[] */
        $productsById = [];
        /** @var $productsById \Model\Product\Service\Entity[] */
        $servicesById = [];
        /** @var $productsById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        foreach ($itemsByBanner as $items) {
            foreach ($items as $item) {
                /** @var $item \Model\Banner\Item\Entity */
                if ($item->getProductId()) $productsById[$item->getProductId()] = null;
                if ($item->getServiceId()) $servicesById[$item->getServiceId()] = null;
                if ($item->getProductCategoryId()) $categoriesById[$item->getProductCategoryId()] = null;
            }
        }

        // подготовка 2-го пакета запросов
        // запрашиваем товары
        if ((bool)$productsById) {
            \RepositoryManager::product()->prepareCollectionById(array_keys($productsById), $region, function($data) use (&$productsById) {
                foreach ($data as $item) {
                    $productsById[(int)$item['id']] = new \Model\Product\BasicEntity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить товары для баннеров');
            });
        }
        // запрашиваем услуги
        if ((bool)$servicesById) {
            \RepositoryManager::service()->prepareCollectionById(array_keys($servicesById), $region, function($data) use (&$servicesById) {
                foreach ($data as $item) {
                    $servicesById[(int)$item['id']] = new \Model\Product\Service\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить услуги для баннеров');
            });
        }
        // запрашиваем категории товаров
        if ((bool)$categoriesById) {
            \RepositoryManager::productCategory()->prepareCollectionById(array_keys($categoriesById), $region, function($data) use (&$categoriesById) {
                foreach ($data as $item) {
                    $categoriesById[(int)$item['id']] = new \Model\Product\Category\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить категории товаров для баннеров');
            });
        }

        if ((bool)$productsById || (bool)$servicesById || (bool)$categoriesById) {
            // выполнение 2-го пакета запросов
            $client->execute();
        }

        // формируем ссылки для баннеров
        // TODO: перенести код в метод репозитория
        foreach ($bannerData as $i => &$item) {
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
                        $url = $router->generate('product', ['productPath' => $product->getPath()]);
                    } else {
                        $barcodes = array_map(function ($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $products);
                        $url = $router->generate('product.set', [
                            'productBarcodes' => implode(',', $barcodes),
                        ]);
                    }
                } else if ($bannerItem->getServiceId()) {
                    \App::logger()->error('Услуги для баннера еще не реализованы');
                } else if ($bannerItem->getProductCategoryId()) {
                    $category = reset($categoriesById);
                    if (!$category instanceof \Model\Product\Category\Entity) {
                        \App::logger()->error(sprintf('Категория #%s не найдена', $bannerItem->getProductCategoryId()));
                        continue;
                    }

                    $url = $router->generate('product.category', ['categoryPath' => $category->getPath()]);
                }
            }

            /*if (!$url && !$item['url']) {
                \App::logger()->error(sprintf('Невалидный баннер %s', json_encode((array)$item, JSON_UNESCAPED_UNICODE)));
                unset($bannerData[$i]);
                continue;
            }*/

            $item['url'] = $url;
        } if (isset($item)) unset($item);

        $bannerData = array_values($bannerData);

        $page = new \View\Main\IndexPage();
        $page->setParam('bannerData', $bannerData);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('myThingsData', [
            'EventType' => 'MyThings.Event.Visit',
            'Action'    => '200'
        ]);

        return new \Http\Response($page->show());
    }
}
