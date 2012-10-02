<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        list($categoryToken, $productToken) = explode('/', $productPath);

        $product = \RepositoryManager::getProduct()->getEntityByToken($productToken);
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар с токеном "%s" не найден.', $productToken));
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('product', $product);

        //var_dump($product);

        return new \Http\Response($page->show());
    }
}