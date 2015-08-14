<?php

namespace Controller\Main;

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
        \RepositoryManager::banner()->prepareCollection(function ($data) use (&$bannerData, &$itemsByBanner) {
            $timeout = \App::config()->banner['timeout'];

            foreach ($data as $i => $item) {
                $bannerId = isset($item['uid']) ? (int)$item['uid'] : null;
                $bannerData[] = [
                    'id'    => $bannerId,
                    'alt'   => $item['name'],
                    'imgs'  => null,
                    'imgb'  => null,
                    'url'   => null,
                    't'     => $timeout,
                    'ga'    => $bannerId . ' - ' . $item['name'],
                    'pos'   => $i,
                ];
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        // товары, услуги, категории
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        $medias = [];
        /** @var $categoriesById \Model\Product\Category\Entity[] */

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
                \App::logger()->error('Не удалось получить товары');
            });

            \RepositoryManager::product()->prepareProductsMediasByIds(array_keys($productsById), $medias);
        }

        if ((bool)$productsById) {
            // выполнение 2-го пакета запросов
            $client->execute();

            \RepositoryManager::product()->enrichProductsFromScms($productsById, 'media label');
            $client->execute();
        }

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
