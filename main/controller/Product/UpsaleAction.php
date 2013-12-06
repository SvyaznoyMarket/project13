<?php

namespace Controller\Product;

class UpsaleAction extends BasicRecommendedAction {
    protected $retailrocketMethodName = 'CrossSellItemToItems';
    protected $actionTitle = 'С этим товаром также покупают';
    protected $name = 'upsale';

    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request)
    {
        \App::logger()->debug('Exec ' . __METHOD__);

        $responseData = [];

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $key = \App::abTest()->getCase()->getKey();

            \App::logger()->info(sprintf('abTest.key=%s, response.cookie.switch=%s', $key, $request->cookies->get('switch')));

            //получаем ids связанных товаров
            $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);

            $products = [];
            if (!empty($relatedId)) {
                $products = \RepositoryManager::product()->getCollectionById($relatedId);
            }

            if (empty($products)) {
                $products = $this->getProductsFromRetailrocket($product, $request, $this->retailrocketMethodName); // UPD
            }

            if ( !is_array($products) ) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title' => $this->actionTitle,
                    'products' => $products,
                ]),
            ];

        } catch (\Exception $e) {
            \App::logger()->error($e, [$this->actionType]);
            return new \Http\JsonResponse([
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ]);
        }

        return new \Http\JsonResponse($responseData);
    }
}