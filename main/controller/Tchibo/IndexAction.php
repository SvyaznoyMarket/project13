<?php

namespace Controller\Tchibo;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $promoRepository = \RepositoryManager::promo();
        $region = $user->getRegion();

        $slideData = [];
        $categoryToken = 'tchibo';
        $category = null;
        $promo = null;
        $content = null;
        $bannerBottom = null;

        // подготовка для 1-го пакета запросов в ядро
        // promo
        $promoRepository->prepareEntityByToken($categoryToken, function($data) use (&$promo, &$categoryToken) {
            if (is_array($data)) {
                $data['token'] = $categoryToken;
                $promo = new \Model\Promo\Entity($data);
            }
        });

        // content
        \App::contentClient()->addQuery(
            'tchibo_root',
            [],
            function($data) use (&$content) {
                if (!empty($data['content'])) {
                    $content = $data['content'];
                }
            },
            function(\Exception $e) {
                \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                \App::exception()->add($e);
            }
        );

        // получим данные category
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });

        // выполнение 1-го пакета запросов в ядро
        $client->execute();

        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $categoryToken));
        }
        /** @var $promo     \Model\Promo\Entity */

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $categoryToken));
        }
        /** @var $category  \Model\Product\Category\Entity */



        // получаем catalog json для категории
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson($category);



        // подготовка для 2-го пакета запросов в ядро
        // получим данные для меню
        $rootCategoryIdInMenu = null;
        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($category->getId(), $region, 3, function($data) use (&$rootCategoryInMenu) {
            $data = is_array($data) ? reset($data) : [];
            if (isset($data['id'])) {
                $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
            }
        });

        $products = [];
        $productsIds = [];
        // перевариваем данные изображений
        // используя айдишники товаров из секции image.products, получим мини-карточки товаров для баннере
        foreach ($promo->getImage() as $image) {
            $productsIds = array_merge($productsIds, $image->getProducts());
        }
        $productsIds = array_unique($productsIds);
        if (count($productsIds) > 0) {
            \RepositoryManager::product()->prepareCollectionById($productsIds, $region, function ($data) use (&$products) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\Entity($item);
                }
            });
        }

        // выполнение 2-го пакета запросов в ядро
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);



        // перевариваем данные изображений для слайдера в $slideData
        foreach ($promo->getImage() as $image) {
            $slideData[] = [
                'imgUrl'  => \App::config()->dataStore['url'] . 'promo/' . $promo->getToken() . '/' . trim($image->getUrl(), '/'),
                'title'   => $image->getName(),
                'linkUrl' => $image->getLink()?($image->getLink().'?from='.$promo->getToken()):'',
                'time'    => $image->getTime() ? $image->getTime() : 3000,
                // Пока не нужно, но в будущем, возможно понадобится делать $repositoryPromo->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
            ];
        }

        if (isset($catalogJson['promo_token'])) {
            $catalogJson['promo_token'] = trim((string)$catalogJson['promo_token']);
        }

        if (!empty($catalogJson['promo_token'])) {
            \App::contentClient()->addQuery(
                $catalogJson['promo_token'],
                [],
                function($data) use (&$bannerBottom) {
                    if (!empty($data['content'])) {
                        $bannerBottom = $data['content'];
                    }
                },
                function(\Exception $e) {
                    \App::logger()->error(sprintf('Не получено содержимое для баннера %s', \App::request()->getRequestUri()));
                    \App::exception()->remove($e);
                }
            );
            \App::contentClient()->execute();
        }

        // формируем вьюху, передаём ей данные
        $page = new \View\Tchibo\IndexPage();
        $page->setParam('slideData', $slideData);
        $page->setParam('catalogConfig', $catalogJson);
        $page->setParam('content', $content);
        $page->setParam('catalogCategories', $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : []);
        $page->setGlobalParam('rootCategoryInMenu', $rootCategoryInMenu);
        $page->setGlobalParam('bannerBottom', $bannerBottom);
        $page->setGlobalParam('products', $products);

        return new \Http\Response($page->show());
    }
}