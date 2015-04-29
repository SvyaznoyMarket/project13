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
        $region = \App::user()->getRegion();

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
        $medias = [];
        foreach (array_chunk(array_values($productIdsByCategoryId), \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                foreach ((array)$data as $item) {
                    if (empty($item['id'])) continue;

                    $product = new \Model\Product\Entity($item);
                    // если товар недоступен для покупки - пропустить
                    if (!$product->isAvailable() || $product->isInShopShowroomOnly()) continue;

                    $productsById[$product->getId()] = $product;
                }
            });

            \RepositoryManager::product()->prepareProductsMediasByIds($productsInChunk, $medias);
        }

        \App::coreClientV2()->execute();

        \RepositoryManager::product()->setMediasForProducts($productsById, $medias);

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