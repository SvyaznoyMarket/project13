<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        list(, $productToken) = explode('/', $productPath);

        $productRepository = \RepositoryManager::getProduct();
        $product = $productRepository->getEntityByToken($productToken);
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар с токеном "%s" не найден.', $productToken));
        }

        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'name' => 'Каталог товаров',
            'url'  => '/catalog/',//$this->generateUrl('productCatalog'),
        );

        foreach ($product->getCategory() as $category) {
            $breadcrumbs[] = array(
                'name' => $category->getName(),
                'url'  => $category->getLink(),
            );
        }
        $breadcrumbs[] = array(
            'name' => $product->getName(),
            'url'  => $product->getLink(),
        );

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

        $accessories = array();
        $related = array();

        $productRepository->setEntityClass('\\Model\\Product\\CompactEntity');
        if (count($product->getAccessoryId())) {
            $accessories = $productRepository->getCollectionById(array_slice($product->getAccessoryId(), 0, 10));
        }

        if (count($product->getRelatedId())) {
            $related = $productRepository->getCollectionById(array_slice($product->getRelatedId(), 0, 10));
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('product', $product);
        $page->setParam('title', $product->getName());
        $page->setParam('breadcrumbs', $breadcrumbs);
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);

        return new \Http\Response($page->show());
    }
}