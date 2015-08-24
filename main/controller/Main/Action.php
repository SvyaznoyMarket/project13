<?php

namespace Controller\Main;

use Model\Banner\BannerEntity;

class Action {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function index(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        // подготовка 1-го пакета запросов
        // запрашиваем баннеры
        $banners = [];
        \RepositoryManager::banner()->prepareCollection(function ($data) use (&$banners, &$itemsByBanner) {

            foreach ($data as $item) {
                $banners[] = new BannerEntity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

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
        $page->setParam('banners', $banners);
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
