<?php

namespace Controller\Product;

class LineAction {
    /**
     * @param $lineToken
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($lineToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $line = \RepositoryManager::line()->getEntityByToken($lineToken);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Серия @%s не найдена.', $lineToken));
        }

        // вид списка других товаров в серии
        $productView = $request->get('view', 'compact');
        $productRepository = \RepositoryManager::product();

        $mainProduct = $productRepository->getEntityById($line->getMainProductId());

        //Собираем все id для товров: наборов серии, простых товаров серии
        $productInLineIds = array_merge($line->getKitId(), $line->getProductId());
        $productsInLine = array_flip($productInLineIds);
        $productRepository->setEntityClass('\Model\Product\ExpandedEntity');

        $globalProducts = [];
        $productRepository->prepareCollectionById($productInLineIds, \App::user()->getRegion(), function($data) use(&$globalProducts) {
            foreach ($data as $item) {
                $globalProducts[] = new \Model\Product\ExpandedEntity($item);
            }
        });
        \App::coreClientV2()->execute();

        foreach ($globalProducts as $product) {
            if (isset($productsInLine[$product->getId()])) {
                $productsInLine[$product->getId()] = $product;
            }
        }

        // фильтрация связанных товаров
        $productsInLine = array_filter($productsInLine, function ($product) {
            return $product instanceof \Model\Product\ExpandedEntity;
        });

        //Запрашиваю составные части набора
        $parts = [];
        if ((bool)$mainProduct->getKit()) {
            $productRepository->setEntityClass('\Model\Product\CompactEntity');
            $partId = [];
            foreach ($mainProduct->getKit() as $part) {
                $partId[] = $part->getId();
            }
            try {
                $parts = $productRepository->getCollectionById($partId);
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $parts = [];
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