<?php

namespace Controller\Product;

use Exception\NotFoundException;
use Model\RetailRocket\RetailRocketRecommendation;

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
            if (!\App::config()->product['pushRecommendation']) {
                throw new NotFoundException('Рекомендации отключены');
            }

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
            $recommendation = null;

            try {
                if (\App::abTest()->isRichRelRecommendations()) {
                    $richResponse = \App::richRelevanceClient()->query(
                        'recsForPlacements',
                        [
                            'placements' => 'add_to_cart_page.one',
                            'productId' => $productId,
                        ]
                    );
                    if (isset($richResponse['add_to_cart_page.one'])) {
                        $recommendation = $richResponse['add_to_cart_page.one'];
                    }
                } else {
                    $client = \App::retailrocketClient();
                    $ids = $client->query('Recomendation/' . $this->retailrocketMethodName, $product ? $product->id : null);
                    $recommendation = new RetailRocketRecommendation([
                        'products'  => $ids,
                        'placement' => 'upsale',
                        'message'   => 'С этим товаром покупают'
                    ]);
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['upsale']);
            }

            if ($recommendation) {
                $products = $recommendation->getProductsById();
            }

            if (!$products) {
                throw new \Exception('Not fount related IDs for this product.');
            }

            \RepositoryManager::product()->prepareProductQueries($products, 'model media label');

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
                'content' => \App::closureTemplating()->render(
                    'product-page/blocks/slider',
                    [
                        'title'    => $recommendation->getMessage(),
                        'products' => $products,
                        'class'    => 'goods-slider--top',
                        'sender'   => [
                            'name'     => $recommendation->getSenderName(),
                            'position' => $recommendation->getPlacement(),
                            'method'   => '',
                        ],
                        'sender2'      => (string)$request->get('sender2'),
                    ]
                ),
                'data' => [
                    'id'              => $product->getId(), // идентификатор товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                    'method'          => $this->retailrocketMethodName, // название алгоритма по которому сформированны рекомендации (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                    'recommendations' => $recommendation, // массив идентификаторов рекомендованных товаров, полученных от Retail Rocket
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