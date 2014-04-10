<?php

namespace Controller\Product;

class UpsaleAction extends BasicRecommendedAction {
    protected $retailrocketMethodName = 'CrossSellItemToItems';
    protected $actionTitle = 'С этим товаром также покупают';
    protected $name = 'upsale';

    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function getResponseData($productId, \Http\Request $request) {
        $responseData = [];

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $key = \App::abTest()->getCase()->getKey();
            $relatedId = null;
            $products = null;

            \App::logger()->info(sprintf('abTest.key=%s, response.cookie.switch=%s', $key, $request->cookies->get('switch')));

            // получаем ids связанных товаров
            // SITE-2818 Список связанных товаров дозаполняем товарами, полученными от RR по методу CrossSellItemToItems
            $recommendationRR = $this->getProductsIdsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            if (is_array($recommendationRR)) {
                $relatedId = array_unique(array_merge($product->getRelatedId(), $recommendationRR));
            }

            if (empty($relatedId)) {
                throw new \Exception('Not fount related IDs for this product.');
            } else {
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
                    'isRetailrocketRecommendation' => true,
                    'retailrocketMethod' => $this->retailrocketMethodName,
                    'retailrocketIds' => $recommendationRR,
                ]),
                'data' => [
                    'id' => $product->getId(),//идентификатор товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                    'method' => $this->retailrocketMethodName,//название алгоритма по которому сформированны рекомендации (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                    'recommendations' => $recommendationRR,//массив идентификаторов рекомендованных товаров, полученных от Retail Rocket
                ],
            ];

        } catch (\Exception $e) {
            \App::logger()->error($e, [$this->actionType]);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return $responseData;
    }
}