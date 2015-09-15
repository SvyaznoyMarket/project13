<?php

namespace Controller\Product;

class Variation {

    /**
     * @param string $productUi
     * @param string $variationId
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function execute($productUi, $variationId, \Http\Request $request) {

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
            call_user_func(function() use(&$contentHtml, $product, $variationId) {
                if ($product->getModel()) {
                    $property = $product->getModel()->getPropertyById($variationId);
                    if ($property) {
                        // TODO: удалить данный код после релиза CORE-3159
                        call_user_func(function() use(&$property) {
                            /** @var \Model\Product\Entity[] $optionProductsById */
                            $optionProductsById = [];
                            foreach ($property->getOption() as $option) {
                                $optionProductsById[$option->product->id] = $option->product;
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

                        $variation = (new \View\Product\Variation())->execute(new \Helper\TemplateHelper(), $product, $property);
                        if ($variation) {
                            $contentHtml = \App::mustache()->render('product/variation', $variation);
                        }
                    }
                }
            });

            $responseData = [
                'contentHtml' => $contentHtml,
            ];

            return new \Http\JsonResponse($responseData);
        } catch (\Exception $e) {
            return new \Http\JsonResponse([
                'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ], \Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}