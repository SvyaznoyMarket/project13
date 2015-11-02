<?php
namespace Controller\ProductCategory\Grid;

use Http\Request;
use Http\Response;
use Model\Product\Category\Entity as Category;
use View\ProductCategory\Grid\AutoGridPage;
use Model\Promo\Entity as Promo;

class AutoGridAction
{
    public function execute(Request $request, Category $category)
    {
        $page = new AutoGridPage();
        $client = \App::coreClientV2();
        $promoRepository = \RepositoryManager::promo();
        $region = \App::user()->getRegion();

        /** @var $promo Promo */
        $promo = null;
        $slideData = [];
        $categoryTree = null;

        // подготовка для 1-го пакета запросов в ядро
        if ($category->isTchibo()) {
            // promo
            $tchiboPromoToken = 'tchibo';
            $promoRepository->prepareByToken($tchiboPromoToken, function ($data) use (&$promo, $tchiboPromoToken) {
                $data = isset($data[0]['uid']) ? $data[0] : null;
                if (is_array($data)) {
                    $data['token'] = $tchiboPromoToken;
                    $promo = new Promo($data);
                }
            });
        }

        \RepositoryManager::productCategory()->prepareTreeCollection($region, 1, 1, function($data) use (&$categoryTree) {
            $categoryTree = $data;
        });

        /** @var Category[] $categoryWithChilds */
        $categoryWithChilds = null;
        // Получаем дочерние категории с category_grid изображениями
        \RepositoryManager::productCategory()->prepareCategoryTree(
            'root_slug',
            $category->getToken(),
            1,
            false,
            false,
            true,
            $categoryWithChilds);

        // выполнение 1-го пакета запросов в ядро
        $client->execute();

        // подготовка для 2-го пакета запросов в ядро
        // получим данные для меню
        /** @var \Model\Product\Category\TreeEntity $rootCategoryInMenu */
        $rootCategoryInMenu = null;

        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot(
            $category->getRootOfParents()->getId(),
            $region,
            3,
            function($data) use (&$rootCategoryInMenu) {
                $data = is_array($data) ? reset($data) : [];
                if (isset($data['id'])) {
                    $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
                }
            }
        );

        // выполнение 2-го пакета запросов в ядро
        $client->execute();

        $catalogCategories = $rootCategoryInMenu->findChild($category->ui);

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
                'linkUrl'  => $promoPage->getLink() ? ($promoPage->getLink() . '?from=' . $promo->getToken()) : '',
                'time'     => $promoPage->getTime() ? : 3000,
                'products' => $itemProducts,
                // Пока не нужно, но в будущем, возможно понадобится делать $repositoryPromo->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
            ];
        }

        $page->setParam('category', $category);
        $page->setParam('categoryWithChilds', $categoryWithChilds);
        $page->setParam('catalogCategories', $catalogCategories ? $catalogCategories->getChild() : []);
        $page->setParam('slideData', $slideData);
        $page->setGlobalParam('rootCategoryInMenu', $rootCategoryInMenu);
        return new Response($page->show());
    }
}
