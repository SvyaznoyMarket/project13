<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        list(, $productToken) = explode('/', $productPath);

        $product = \RepositoryManager::getProduct()->getEntityByToken($productToken);
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

        $page = new \View\Product\IndexPage();
        $page->setParam('product', $product);
        $page->setParam('title', $product->getName());
        $page->setParam('breadcrumbs', $breadcrumbs);

        return new \Http\Response($page->show());
    }
}