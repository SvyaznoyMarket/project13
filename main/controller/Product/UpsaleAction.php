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
    public function execute($productId, \Http\Request $request, $returnJson = true)
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

            // получаем ids связанных товаров
            // SITE-2818 Список связанных товаров дозаполняем товарами, полученными от RR по методу CrossSellItemToItems
            $recommendationRR = $this->getProductsIdsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            $relatedId = array_unique(array_merge($product->getRelatedId(), $recommendationRR));

            $products = null;
            if (!empty($relatedId)) {
                $products = \RepositoryManager::product()->getCollectionById($relatedId);
            }

            // SITE-2818 Из блока "С этим товаром также покупают" убраем товары, которые есть только в магазинах ("Резерв" и витринные)
            foreach ($products as $key => $item) {
                if ($item->isInShopOnly() || $item->isInShopStockOnly()) {
                    unset($products[$key]);
                }
            }
            $products = array_slice($products, 0, \App::config()->product['itemsInSlider'] * 2);

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

            $responseData = [
                    'success' => false,
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return $returnJson
            ? new \Http\JsonResponse($responseData)
            : $responseData;
    }
}