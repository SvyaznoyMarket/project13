<?php

namespace Mobile\Controller\Main;

class IndexAction {
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $region = \App::user()->getRegion();
        $bannerRepository = \RepositoryManager::banner();

        $banners = $bannerRepository->getCollection($region);

        // получение товаров, категорий товаров и услуг из элементов для каждого баннера
        $productsById = [];
        $categoriesById = [];
        $servicesById = [];
        foreach ($banners as $banner) {
            foreach ($banner->getItem() as $item) {
                // товар
                if ($item->getProductId()) {
                    $productsById[$item->getProductId()] = null;
                // категория товара
                } else if ($item->getProductCategoryId()) {
                    $categoriesById[$item->getProductCategoryId()] = null;
                // услуга
                } else if ($item->getServiceId()) {
                    $servicesById[$item->getServiceId()] = null;
                }
            }
        }

        // подготовка 1-го пакета запросов
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
            // выполнение 1-го пакета запросов
            $client->execute();
        }

        foreach ($banners as $banner) {
            $bannerRepository->setEntityUrl($banner, \App::router(), $productsById, $categoriesById, $servicesById);
        }

        $page = new \Mobile\View\Main\IndexPage();
        $page->setParam('banners', $banners);

        return new \Http\Response($page->show());
    }
}
