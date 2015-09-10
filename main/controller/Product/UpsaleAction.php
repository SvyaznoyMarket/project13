<?php

namespace Controller\Product;

class UpsaleAction extends BasicRecommendedAction {
    protected $retailrocketMethodName = 'CrossSellItemToItems';
    protected $actionTitle = 'С этим товаром покупают';
    protected $name = 'upsale';

    use ProductHelperTrait;

    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return array
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request) {

        try {
            /** @var \Model\Product\Entity $product */
            call_user_func(function() use(&$product, $productId) {
                /** @var \Model\Product\Entity[] $products */
                $products = [new \Model\Product\Entity(['id' => $productId])];
                \RepositoryManager::product()->prepareProductQueries($products);
                \App::coreClientV2()->execute();

                if (!$products) {
                    throw new \Exception(sprintf('Товар #%s не найден', $productId));
                }

                $product = $products[0];
            });

            /** @var \Model\Product\Entity[] $products */
            $products = [];

            // получаем ids связанных товаров
            // SITE-2818 Список связанных товаров дозаполняем товарами, полученными от RR по методу CrossSellItemToItems
            $recommendationRR = $this->getProductsIdsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            if (is_array($recommendationRR)) {
                $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, array_filter(array_unique(array_merge($product->getRelatedId(), $recommendationRR))));
            }

            if (!$products) {
                throw new \Exception('Not fount related IDs for this product.');
            }

            $this->getRetailrocketMethodName();

            \RepositoryManager::product()->prepareProductQueries($products, 'media label');

            \App::coreClientV2()->execute();

            // SITE-2818 Из блока "С этим товаром покупают" убраем товары, которые есть только в магазинах ("Резерв" и витринные)
            $products = array_filter($products, function(\Model\Product\Entity $product) {
                return ($product->isAvailable() && $product->getIsBuyable() && !$product->isInShopShowroomOnly() && !$product->isInShopOnly() && !$product->isInShopStockOnly() && 5 != $product->getStatusId());
            });

            // SITE-4710 Рекомендации выдают несколько размеров одного и того же товара
            $products = $this->filterByModelId($products);

            $products = array_slice($products, 0, \App::config()->product['itemsInSlider'] * 2);

            if (!$products) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                    'title'    => $this->actionTitle,
                    'products' => $products,
                    'class'    => 'goods-slider--top',
                    'sender'   => [
                        'name'     => 'retailrocket',
                        'position' => 'AddBasket',
                        'method'   => $this->retailrocketMethodName,
                    ],
                    'sender2'      => (string)$request->get('sender2'),
                ]),
                'data' => [
                    'id'              => $product->getId(),//идентификатор товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                    'method'          => $this->retailrocketMethodName,//название алгоритма по которому сформированны рекомендации (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                    'recommendations' => $recommendationRR,//массив идентификаторов рекомендованных товаров, полученных от Retail Rocket
                ],
            ];

        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}