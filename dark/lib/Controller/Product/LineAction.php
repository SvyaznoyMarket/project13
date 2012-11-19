<?php

namespace Controller\Product;

class LineAction {
    public function execute($lineToken, \Http\Request $request) {
        $line = \RepositoryManager::getLine()->getEntityByToken($lineToken);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Серия с токеном "%s" не найдена.', $lineToken));
        }

        // вид списка других товаров в серии
        $productView = $request->get('view', 'compact');
        $productRepository = \RepositoryManager::getProduct();

        $mainProduct = $productRepository->getEntityById($line->getMainProductId());

        //Собираем все id для товров: наборов серии, простых товаров серии
        $productInLineId = array_merge($line->getKitId(), $line->getProductId());
        $productsInLine = array_flip($productInLineId);
        $productRepository->setEntityClass( '\Model\Product\ExpandedEntity');

        try {
            $globalProducts = $productRepository->getCollectionById($productInLineId);
        } catch (\Exception $e) {
            \App::$exception = $e;
            \App::logger()->error($e);

            $globalProducts = array();
        }

        foreach ($globalProducts as $product) {
            if (isset($productsInLine[$product->getId()])) {
                $productsInLine[$product->getId()] = $product;
            }
        }

        //Запрашиваю составные части набора
        $parts = array();
        if ((bool)$mainProduct->getKit()) {
            $productRepository->setEntityClass( '\Model\Product\CompactEntity');
            $partId = array();
            foreach ($mainProduct->getKit() as $part) {
                $partId[] = $part->getId();
            }
            try {
                $parts = $productRepository->getCollectionById($partId);
            } catch (\Exception $e) {
                \App::$exception = $e;
                \App::logger()->error($e);

                $parts = array();
            }
        }

        if ((bool)$productsInLine) {
            $productPager = new \Iterator\EntityPager(array_values($productsInLine), count($productsInLine));
            $productPager->setMaxPerPage(count($productsInLine));
        } else {
            $productPager = null;
        }

        $page = new \View\Product\LinePage();
        $page->setParam('line', $line);
        $page->setParam('mainProduct', $mainProduct);
        $page->setParam('parts', $parts);
        $page->setParam('productView', $productView);
        $page->setParam('productPager', $productPager);
        $page->setParam('title', 'Серия ' . $line->getName());

        return new \Http\Response($page->show());
    }
}