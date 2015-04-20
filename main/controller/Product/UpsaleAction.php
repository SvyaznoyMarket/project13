<?php

namespace Controller\Product;

use Model\Product\Entity;

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
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $relatedId = null;
            $products = null;

            // получаем ids связанных товаров
            // SITE-2818 Список связанных товаров дозаполняем товарами, полученными от RR по методу CrossSellItemToItems
            $recommendationRR = $this->getProductsIdsFromRetailrocket($product, $request, $this->retailrocketMethodName);
            if (is_array($recommendationRR)) {
                $relatedId = array_unique(array_merge($product->getRelatedId(), $recommendationRR));
            }

            $collection = [];
            if (empty($relatedId)) {
                throw new \Exception('Not fount related IDs for this product.');
            } else {
                /**
                 * Для всех продуктов расставим и запомним источники (движок, Engine) рекомендаций
                 */
                foreach($relatedId as $id) {
                    $recommEngine[$id] = [
                        'id'        => $id,
                        'engine'    => $this->getEngine() ?: $this->getName(),
                        'name'      => $this->getName(),
                        'method'    => $this->getRetailrocketMethodName(),
                    ];
                }
                foreach ($product->getRelatedId() as $id) {
                    $recommEngine[$id] = [
                        'id'        => $id,
                        'engine'    => 'enter',
                        'name'      => 'enter',
                        'method'    => null,
                    ];
                }

                $chunckedIds = array_chunk($relatedId, \App::config()->coreV2['chunk_size']);
                foreach ($chunckedIds as $chunk) {
                    \RepositoryManager::product()->prepareCollectionById($chunk, \App::user()->getRegion(),
                        function($data) use(&$collection) {
                            foreach ($data as $value) {
                                if (!isset($value['id']) || !isset($value['link'])) continue;
                                $entity = new \Model\Product\Entity($value);
                                $collection[$entity->getId()] = $entity;
                            }
                        });
                }
                \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
            }

            $products = [];
            foreach ($relatedId as $id) {
                if (!isset($collection[$id])) continue;
                $products[] = $collection[$id];
            }

            // SITE-2818 Из блока "С этим товаром покупают" убраем товары, которые есть только в магазинах ("Резерв" и витринные)
            foreach ($products as $key => $item) {
                /** @var Entity $item */
                if ($item->isInShopOnly() || $item->isInShopStockOnly() || !$item->getIsBuyable() || (5 == $item->getStatusId())) {
                    unset($products[$key]);
                }
            }

            // SITE-4710 Рекомендации выдают несколько размеров одного и того же товара
            $products = $this->filterByModelId($products);

            $products = array_slice($products, 0, \App::config()->product['itemsInSlider'] * 2);

            if ( !(bool)$products ) {
                throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $this->actionType));
            }

            $responseData = [
                'success' => true,
                'content' => \App::closureTemplating()->render('product/__slider', [
                    'title'    => $this->actionTitle,
                    'products' => $products,
                    'count'    => count($products),
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