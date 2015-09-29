<?php

namespace Controller\Category\Listing;

use EnterApplication\CurlTrait;

class Product {
    use CurlTrait;
    /**
     * @param string $categoryUi
     * @param string $productUi
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function execute($categoryUi, $productUi, \Http\Request $request) {
        $cartButtonSender = is_array($request->query->get('cartButtonSender')) ? $request->query->get('cartButtonSender') : [];

        try {
            /** @var \Model\Product\Entity[] $products */
            $products = [new \Model\Product\Entity(['ui' => $productUi])];
            \RepositoryManager::product()->prepareProductQueries($products, 'model media label brand category property');

            /** @var $category \Model\Product\Category\Entity|null */
            $category = null;
            \RepositoryManager::productCategory()->prepareEntityByUid($categoryUi, function($data) use (&$category) {
                if ($data) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });

            \App::coreClientV2()->execute();

            if (!$products) {
                throw new \Exception('Товар ' . $productUi . ' не найден');
            }

            /** @var \Model\Favorite\Product\Entity[] $favoriteProductsByUi */
            $favoriteProductsByUi = [];
            call_user_func(function() use (&$products, &$favoriteProductsByUi) {
                $userUi = \App::user()->getEntity() ? \App::user()->getEntity()->getUi() : null;
                if (!$userUi) return;
                $productUis = array_map(function(\Model\Product\Entity $product) { return $product->ui; }, $products);
                if (!$productUis) return;

                $favoriteQuery = new \EnterQuery\User\Favorite\Check($userUi, $productUis);
                $favoriteQuery->prepare();

                $this->getCurl()->execute();

                // избранные товары
                $favoriteProductsByUi = [];
                foreach ($favoriteQuery->response->products as $item) {
                    if (!isset($item['is_favorite']) || !$item['is_favorite']) continue;

                    $ui = isset($item['uid']) ? (string)$item['uid'] : null;
                    if (!$ui) continue;

                    $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
                }
            });

            // получение ui для моделей
            // TODO удалить данный код после релиза CORE-3159
            call_user_func(function() use(&$contentHtml, $products) {
                if (!$products[0]->getModel()) {
                    return;
                }

                /** @var \Model\Product\Entity[] $optionProductsById */
                $optionProductsById = [];
                foreach ($products[0]->getModel()->getProperty() as $property) {
                    foreach ($property->getOption() as $option) {
                        $optionProductsById[$option->product->id] = $option->product;
                    }
                }

                \App::coreClientV2()->addQuery(
                    'product/get-v3',
                    [
                        'select_type' => 'id',
                        'id' => array_keys($optionProductsById),
                        'geo_id' => \App::user()->getRegion()->id,
                        'withModels' => 0,
                        'withRelated' => 0,
                    ],
                    [],
                    function($data) use(&$optionProductsById) {
                        if (is_array($data)) {
                            foreach ($data as $item) {
                                $product = new \Model\Product\Entity($item);
                                $optionProductsById[$product->id]->ui = $product->ui;
                            }
                        }
                    }
                );

                \App::coreClientV2()->execute();
            });

            return new \Http\JsonResponse([
                'product' => (new \View\Product\ShowAction())->execute(
                    new \Helper\TemplateHelper(),
                    $products[0],
                    null,
                    true,
                    new \View\Cart\ProductButtonAction(),
                    new \View\Product\ReviewCompactAction(),
                    'product_200',
                    $cartButtonSender,
                    $category,
                    isset($favoriteProductsByUi[$products[0]->ui]) ? $favoriteProductsByUi[$products[0]->ui] : null
                ),
            ]);
        } catch (\Exception $e) {
            return new \Http\JsonResponse([
                'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ], \Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}