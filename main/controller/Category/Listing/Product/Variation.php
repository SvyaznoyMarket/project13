<?php

namespace Controller\Category\Listing\Product;

class Variation {

    /**
     * @param string $categoryUi
     * @param string $productUi
     * @param string $variationId
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function execute($categoryUi, $productUi, $variationId, \Http\Request $request) {
        $cartButtonSender = is_array($request->query->get('cartButtonSender')) ? $request->query->get('cartButtonSender') : [];

        try {
            /** @var \Model\Product\Entity[] $products */
            $products = [new \Model\Product\Entity(['ui' => $productUi])];
            \RepositoryManager::product()->prepareProductQueries($products, 'model');
            \App::coreClientV2()->execute();

            if (!$products) {
                throw new \Exception('Товар ' . $productUi . ' не найден');
            }

            $product = $products[0];

            $contentHtml = '';
            // Получение HTML кода опций варианта
            call_user_func(function() use(&$contentHtml, $product, $variationId, $categoryUi, $cartButtonSender) {
                if (!$product->model) {
                    return;
                }

                $property = $product->model->property;
                if (!$property || $property->id != $variationId) {
                    return;
                }

                $variation = (new \View\Category\Listing\Product\Variations\Variation())->execute(new \Helper\TemplateHelper(), $product, $property, $categoryUi, $cartButtonSender);
                if ($variation) {
                    $contentHtml = \App::mustache()->render('product/variation', $variation);
                }
            });

            return new \Http\JsonResponse([
                'contentHtml' => $contentHtml,
            ]);
        } catch (\Exception $e) {
            return new \Http\JsonResponse([
                'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ], \Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}