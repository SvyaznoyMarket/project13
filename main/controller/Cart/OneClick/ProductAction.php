<?php

namespace Controller\Cart\OneClick;

class ProductAction {
    /**
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, \Http\Request $request) {
        $productId = (int)$productId;

        if (!$productId) {
            throw new \Exception\NotFoundException('Товар не найден');
        }

        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['id' => $productId])];
        \RepositoryManager::product()->useV3()->withoutModels()->prepareProductQueries($products);
        \App::coreClientV2()->execute();

        if (!$products) {
            throw new \Exception\NotFoundException('Товар не найден');
        }

        $params = $request->query->all();
        $params['productPath'] = $products[0]->getPath();
        unset($params['shopId']);

        return new \Http\RedirectResponse(\App::router()->generate('product', $params) . '#one-click' . ($request->get('shopId') ? '-' . $request->get('shopId') : ''));
    }
}