<?php


namespace Controller\Product;


use Helper\TemplateHelper;
use View\Helper;

class SmartChoiceAction {

    /**
     * @param array $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        // request type: $.getJSON('/ajax/product-smartchoice',{"products[]":[55092,64686]},function(data){})

        \App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();
        $controller = new \Controller\Product\SimilarAction();
        $helper = new \Helper\TemplateHelper();
        $productIds = [];
        $recommendedIds = [];

        $products = $request->query->get('products');

        if ($products && is_array($products)) {

            // Проверяем существование продуктов
            foreach ($products as $id) {
                $product = \RepositoryManager::product()->getEntityById($id);
                if (!$product) {
                    throw new \Exception(sprintf('Товар #%s не найден', $id));
                }
                $productIds[] = $product->getId();
            }

            // Запрашиваем рекомендации для существующих продуктов
            foreach ($productIds as $id) {
                $queryUrl = "{$rrConfig['apiUrl']}Recomendation/UpSellItemToItems/{$rrConfig['account']}/$id";

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$recommendedIds, $id) {
                    $recommendedIds[$id] = $data;
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }

            \App::curl()->execute(null, 1);

            // Если для продуктов есть рекомендации
            if (count($recommendedIds)) {
                foreach ($recommendedIds as $key => &$value) {
                    \RepositoryManager::product()->prepareCollectionById($value, $region, function ($data) use (&$value) {
                        foreach ($data as $key => &$product) {
                            $product = new \Model\Product\Entity($product);
                            if (!$product instanceof \Model\Product\Entity) continue;
                            if ($product->isInShopOnly() || $product->isInShopStockOnly() || !$product->getIsBuyable()) {
                                unset($data[$key]);
                            }
                        }
                        $value = $data;
                    });
                }
            }
            // Запрашиваем продукты
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            foreach ($recommendedIds as $id => $products) {
                if (count($products)>0) {
                    $recommend[$id] = [
                        'success' => true,
                        'content' => \App::closureTemplating()->render('product/__slider', [
                                'title' => null,
                                'products' => $products,
                                'class' => 'smartChoiceSlider smartChoiceId-' . $id,
                            ]),
                    ];
                }
            }

            $response['success'] = true;
            $response['result'] = $recommend;

        }

        return new \Http\JsonResponse($response);

    }

} 