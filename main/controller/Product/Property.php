<?php

namespace Controller\Product;

class Property {
    /**
     * @param string $productUi
     * @param string $propertyId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($productUi, $propertyId, \Http\Request $request) {
        try {
            /** @var \Model\Product\Entity[] $products */
            $products = [new \Model\Product\Entity(['ui' => $productUi])];
            \RepositoryManager::product()->prepareProductQueries($products, 'property');
            \App::coreClientV2()->execute();

            if (!$products) {
                throw new \Exception('Товар ' . $productUi . ' не найден');
            }

            return new \Http\JsonResponse(call_user_func(function() use($products, $propertyId) {
                $property = $products[0]->getPropertyById($propertyId);
                if ($property && $property->getHint()) {
                    return [
                        'popupHtml' => \App::helper()->render('product-page/blocks/hint/popup', ['value' => $property->getHint()]),
                    ];
                }

                return [];
            }));
        } catch (\Exception $e) {
            return new \Http\JsonResponse([
                'error' => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ], \Http\Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}