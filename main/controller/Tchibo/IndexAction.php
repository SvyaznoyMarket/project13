<?php

namespace Controller\Tchibo;

use Model\Product\Category\Entity as Category;

class IndexAction {

    public function execute(\Http\Request $request) {

        $client = \App::coreClientV2();
        $scmsClient = \App::scmsClient();
        $user = \App::user();
        $promoRepository = \RepositoryManager::promo();
        $region = $user->getRegion();

        $slideData = [];
        $categoryToken = 'tchibo';
        $promo = null;
        $bannerBottom = null;
        $categoryTree = null;
        $promoBanners = null;
        $collectionBanners = null;

        // подготовка для 1-го пакета запросов в ядро
        // promo
        $promoRepository->prepareByToken($categoryToken, function($data) use (&$promo, &$categoryToken) {
            $data = isset($data[0]['uid']) ? $data[0] : null;
            if (is_array($data)) {
                $data['token'] = $categoryToken;
                $promo = new \Model\Promo\Entity($data);
            }
        });

        $promoRepository->prepareByToken('tchibo_main_small_banners', function($data) use (&$promoBanners) {
            $data = isset($data[0]['uid']) ? $data[0] : [];
            if (is_array($data)) {
                $promoBanners = new \Model\Promo\Entity($data);
            }
        });

        $promoRepository->prepareByToken('tchibo_main_small_banners_bottom', function($data) use (&$collectionBanners) {
            $data = isset($data[0]['uid']) ? $data[0] : [];
            if (is_array($data)) {
                $collectionBanners = new \Model\Promo\Entity($data);
            }
        });

        /** @var $category \Model\Product\Category\Entity */
        $category = null;
        $catalogJson = [];
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category, &$catalogJson) {
            if ($data && is_array($data)) {
                $category = new \Model\Product\Category\Entity($data);
                $catalogJson = $category->catalogJson;
            }
        });

        \RepositoryManager::productCategory()->prepareTreeCollection($region, 1, 1, function($data) use (&$categoryTree) {
            $categoryTree = $data;
        });

        /** @var Category[] $categoryWithChilds */
        $categoryWithChilds = null;
        // Получаем дочерние категории с category_grid изображениями
        \RepositoryManager::productCategory()->prepareCategoryTree(
            'root_slug',
            $categoryToken,
            1,
            false,
            false,
            true,
            $categoryWithChilds);

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

        // подготовка для 2-го пакета запросов в ядро
        // получим данные для меню
        /** @var \Model\Product\Category\TreeEntity $rootCategoryInMenu */
        $rootCategoryInMenu = null;
        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($category->getId(), $region, 4, function($data) use (&$rootCategoryInMenu) {
            $data = is_array($data) ? reset($data) : [];
            if (isset($data['id'])) {
                $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
            }
        });

        $productUis = [];
        // перевариваем данные изображений
        // используя айдишники товаров из секции image.products, получим мини-карточки товаров для баннере
        foreach ($promo->getPages() as $promoPage) {
            $uiChunk = [];
            foreach ($promoPage->getProducts() as $product) {
                $uiChunk[] = $product->ui;
            }

            $productUis = array_merge($productUis, $uiChunk);
        }
        $productUis = array_unique($productUis);

        /** @var \Model\Product\Entity[] $productsByUi */
        $productsByUi = [];
        foreach ($productUis as $productUi) {
            $productsByUi[$productUi] = new \Model\Product\Entity(['ui' => $productUi]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsByUi, 'model media label');

        // выполнение 2-го пакета запросов в ядро
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);

        // перевариваем данные изображений для слайдера в $slideData
        foreach ($promo->getPages() as $promoPage) {

            $itemProducts = [];
            foreach($promoPage->getProducts() as $promoProduct) {
                $product = isset($productsByUi[$promoProduct->ui]) ? $productsByUi[$promoProduct->ui] : null;
                if (!$product || !$promoPage->getImageUrl()) continue;

                /** @var $product \Model\Product\Entity */
                $itemProducts[] = [
                    'image'         => $product->getMainImageUrl('product_160'),
                    'link'          => $product->getLink(),
                    'name'          => $product->getName(),
                    'price'         => $product->getPrice(),
                    'isBuyable'     => $product->isAvailable() || $product->hasAvailableModels(),
                    'statusId'      => $product->getStatusId(),
                    'cartButton'    => (new \View\Cart\ProductButtonAction())->execute(new \Helper\TemplateHelper(), $product),
                ];
            }

            $slideData[] = [
                'target'   => '_self',
                'imgUrl'   => $promoPage->getImageUrl(),
                'title'    => $promoPage->getName(),
                'linkUrl'  => $promoPage->getLink()?($promoPage->getLink().'?from='.$promo->getToken()):'',
                'time'     => $promoPage->getTime() ? $promoPage->getTime() : 3000,
                'products' => $itemProducts,
                // Пока не нужно, но в будущем, возможно понадобится делать $repositoryPromo->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
            ];
        }

        if (!empty($catalogJson['promo_token'])) {
            $scmsClient->addQuery(
                'api/static-page',
                [
                    'token' => [$catalogJson['promo_token']],
                    'geo_town_id' => \App::user()->getRegion()->id,
                    'tags' => ['site-web'],
                ],
                [],
                function($data) use (&$bannerBottom) {
                    if (!empty($data['pages'][0]['content'])) {
                        $bannerBottom = $data['pages'][0]['content'];
                    }
                },
                function(\Exception $e) {
                    \App::logger()->error(sprintf('Не получено содержимое для баннера %s', \App::request()->getRequestUri()));
                    \App::exception()->remove($e);
                }
            );
        }

        $promoContent = null;
        $promoToken = 'tchibo_promo';
        $scmsClient->addQuery(
            'api/static-page',
            [
                'token' => [$promoToken],
                'geo_town_id' => \App::user()->getRegion()->id,
                'tags' => ['site-web'],
            ],
            [],
            function($data) use (&$promoContent) {
                if (!empty($data['pages'][0]['content'])) {
                    $promoContent = $data['pages'][0]['content'];
                }
            },
            function(\Exception $e) use (&$promoToken) {
                \App::logger()->error(sprintf('Не получен контентный блок %s', $promoToken));
                \App::exception()->add($e);
            }
        );

        $scmsClient->execute();

        // SITE-3970
        // Стили для названий категорий в меню tchibo
        $tchiboMenuCategoryNameStyles = [];
        if (isset($catalogJson['tchibo_menu']['style']['name']) && is_array($catalogJson['tchibo_menu']['style']['name'])) {
            $tchiboMenuCategoryNameStyles = $catalogJson['tchibo_menu']['style']['name'];
        }

        // формируем вьюху, передаём ей данные
        $page = new \View\Tchibo\IndexPage();
        $page->setParam('category', $category);
        $page->setParam('slideData', $slideData);
        $page->setParam('catalogConfig', $catalogJson);
        $page->setParam('categoryWithChilds', $categoryWithChilds);
        $page->setParam('catalogCategories', $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : []);
        $page->setParam('promoBanners', $promoBanners);
        $page->setParam('collectionBanners', $collectionBanners);
        $page->setGlobalParam('rootCategoryInMenu', $rootCategoryInMenu);
        $page->setGlobalParam('bannerBottom', $bannerBottom);
        $page->setGlobalParam('tchiboMenuCategoryNameStyles', $tchiboMenuCategoryNameStyles);
        $page->setGlobalParam('promoContent', $promoContent);

        return new \Http\Response($page->show());
    }
}