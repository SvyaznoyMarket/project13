<?php

namespace Terminal\Controller\ProductLine;

class IndexAction {
    /**
     * @param $lineId
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($lineId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $line = \RepositoryManager::line()->getEntityById($lineId);
        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Категория #% не найдена', $line->getId()));
        }

        $page = new \Terminal\View\ProductLine\IndexPage();
        $page->setParam('line', $line);

        return new \Http\Response($page->show());
    }

    /**
     * @param integer       $lineId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function product($lineId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $productSorting = new \Model\Product\TerminalSorting();

        $client = \App::coreClientV2();
        $user = \App::user();

        // запрашиваем линию товаров по id
        $line = \RepositoryManager::line()->getEntityById($lineId);

        if (!$line) {
            throw new \Exception\NotFoundException(sprintf('Линия товара #%s не найдена.', $lineId));
        }

        /** @var $collection \Model\Product\TerminalEntity[] */
        $collection = [];
        $entityClass = '\Model\Product\TerminalEntity';
        if (!empty($response['list'])) {
            \RepositoryManager::product()->prepareCollectionById($line->getKitId(), $user->getRegion(), function($data) use(&$collection, $entityClass) {
                foreach ($data as $item) {
                    $collection[] = new $entityClass($item);
                }
            });
        }
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $shopId = \App::config()->region['shop_id'];
        $productData = [];
        foreach ($collection as $product) {
            $productData[] = [
                'id'            => $product->getId(),
                'name'          => $product->getName(),
                'image'         => $product->getImageUrl(3),
                'article'       => $product->getArticle(),
                'price'         => $product->getPrice(),
                'description'   => $product->getTagline(),
                'isBuyable'     => $product->getIsBuyable($shopId),
                'isInShop'      => $product->getIsInShop($shopId),
                'isInShowroom'  => $product->getIsInShowroom($shopId),
                'isInStore'     => $product->getState()->getIsStore(),
                'hasSupplier'   => $product->getState()->getIsSupplier(),
                'isInOtherShop' => $product->getState()->getIsShop(), //
                'line'          => $product->getLine() ? [
                    'id'              =>  $product->getLine()->getId(),
                    'token'           =>  $product->getLine()->getToken(),
                    'name'            =>  $product->getLine()->getName(),
                    'image'           =>  $product->getLine()->getImage(),
                    'kitQuantity'     =>  $product->getLine()->getKitCount(),
                    'productQuantity' =>  $product->getLine()->getProductCount(),
                    'totalQuantity'   =>  $product->getLine()->getTotalCount(),
                ] : null,
            ];
        }

        return new \Http\JsonResponse([
            'success'  => true,
            'products' => $productData,
        ]);
    }
}
