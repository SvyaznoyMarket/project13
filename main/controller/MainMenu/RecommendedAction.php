<?php


namespace controller\MainMenu;


class RecommendedAction {

    /**
     * @param \Http\Request $request
     * @param string $rootCategoryId
     * @param string $childIds
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $rootCategoryId, $childIds) {
        $templating = \App::closureTemplating();
        $client = \App::retailrocketClient();

        $sender = [
            'name'     => 'retailrocket',
            'method'   => 'CategoryToItems',
            'position' => 'ItemOfDay',
        ];

        $categoryIds = explode(',', $childIds);

        /*
        $categoryIds = [];
        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot(
            $rootCategoryId,
            null, // regionId
            2, // max level
            function($data) use (&$categoryIds) {
                if (!isset($data[0]['children'][0])) return; // дочерние элементы первой категории

                foreach ($data[0]['children'] as $item) {
                    if (empty($item['id'])) continue;

                    $categoryIds[] = $item['id'];
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['main_menu', 'recommendation']);
            }
        );
        \App::coreClientV2()->execute(null, 1); // нихай, пускай только один раз запросит, а то еще подвесит сфинкс своими ретраями
        */

        $categoryIds = array_values(array_filter(array_unique($categoryIds)));

        // рекомендации для каждой категории
        $productIdsByCategoryId = [];
        foreach ($categoryIds as $categoryId) {
            $client->addQuery(
                'Recomendation/' . $sender['method'],
                $categoryId,
                [],
                [],
                function($data) use (&$sender, &$productIdsByCategoryId, $categoryId) {
                    if (empty($data[0])) return;

                    $sender['items'][] = $data[0];
                    $productIdsByCategoryId[$categoryId] = $data[0];
                }
            );
        }
        $client->execute(null, 1); // нихай, пускай только один раз запрашивает

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        foreach ($productIdsByCategoryId as $productId) {
            $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media label brand');
        \App::coreClientV2()->execute();

        $productsById = array_filter($productsById, function(\Model\Product\Entity $product) {
            return ($product->isAvailable() && !$product->isInShopShowroomOnly());
        });

        // ответ
        $responseData = [
            'productBlocks' => [],
        ];

        foreach ($productIdsByCategoryId as $categoryId => $productId) {
            if (!$categoryId) continue; // на всякий случай, мало ли...

            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            $responseData['productBlocks'][] = [
                'categoryId' => $categoryId,
                'content'    => $templating->render('common/__navigation-recommend', [
                    'product' => $product,
                    'sender'  => $sender,
                ]),
            ];
        }

        return new \Http\JsonResponse($responseData);
    }

} 