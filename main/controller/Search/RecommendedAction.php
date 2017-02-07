<?php

namespace Controller\Search;

class RecommendedAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $recommendController = new \Controller\Product\RecommendedAction();
        $searchQuery = (new \Controller\Search\Action())->getSearchQueryByRequest($request);
        $sendersByType = $recommendController->getSendersIndexedByTypeByHttpRequest($request);
        $recommendations = \App::richRelevanceClient()->query(
            'recsForPlacements',
            [
                'placements' => 'search_page.noresult',
            ]
        );

        if (!empty($recommendations['search_page.noresult'])) {
            $productsById = $recommendations['search_page.noresult']->getProductsById();
        } else {
            $productsById = [];
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'category label media brand');
        \App::coreClientV2()->execute();
        $productsById = array_filter($productsById, function(\Model\Product\Entity $product) {
            return ($product->isAvailable() && !$product->isInShopShowroomOnly());
        });

        $recommendData = [];
        foreach ($sendersByType as $type => $sender) {
            $products = [];

            if (isset($recommendations)) {
                $products = $productsById;
            }

            if (!$products) {
                $recommendData[$type] = [
                    'success' => false,
                ];
            } else {
                $recommendData[$type] = [
                    'success' => true,
                    'content' => \App::closureTemplating()->render('product/__slider', [
                        'title'     => isset($recommendations['search_page.noresult'])
                            ? $recommendations['search_page.noresult']->getMessage()
                            : $recommendController->getTitleByType($type),
                        'products'  => $products,
                        'class'     => 'slideItem-7item',
                        'sender'    => $sender,
                    ]),
                    'data' => [
                        'id'              => $searchQuery,
                        'method'          => $sender['method'],
                        'recommendations' => $sender['items'],
                    ],
                ];
            }
        }

        return new \Http\JsonResponse([
            'success'   => true,
            'recommend' => $recommendData,
        ]);
    }
}