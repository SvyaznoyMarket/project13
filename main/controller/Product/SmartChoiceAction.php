<?php

namespace Controller\Product;

use \Http\JsonResponse,
    \Http\Request;

class SmartChoiceAction {

    /** Возвращает рекомендации для продуктов
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(Request $request) {
        // request type: $.getJSON('/ajax/product-smartchoice',{"products":[55092,64686]},function(data){})

        //\App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();
        $recommendedProducts = [];

        $products = $request->query->get('products');

        if ($products && is_array($products)) {
            $productIds = [];
            // Проверяем существование продуктов
            foreach ($products as $id) {
                /** @var \Model\Product\Entity[] $backendProducts */
                $backendProducts = [new \Model\Product\Entity(['id' => $id])];
                \RepositoryManager::product()->prepareProductQueries($backendProducts);
                \App::coreClientV2()->execute();

                if (!$backendProducts) {
                    \App::logger()->error(sprintf('Товар #%s не найден', $id), ['SmartChoice']);
                } else {
                    $productIds[] = $backendProducts[0]->getId();
                }
            }

            // Запрашиваем рекомендации для существующих продуктов
            foreach ($productIds as $id) {
                $queryUrl = "{$rrConfig['apiUrl']}Recomendation/UpSellItemToItems/{$rrConfig['account']}/$id";

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$recommendedProducts, $id) {
                    if ($data) {
                        $recommendedProducts[$id] = $data;
                    }
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }

            \App::curl()->execute(null, 1);

            // Если для продуктов есть рекомендации
            call_user_func(function() use (&$recommendedProducts) {
                if (!$recommendedProducts) {
                    return;
                }

                foreach ($recommendedProducts as &$products) {
                    foreach ($products as &$product) {
                        $product = new \Model\Product\Entity(['id' => $product]);
                    }

                    \RepositoryManager::product()->prepareProductQueries($products, 'category label media');
                }

                \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

                foreach ($recommendedProducts as &$products) {
                    $products = array_filter($products, function(\Model\Product\Entity $product) {
                        return $product->getIsBuyable();
                    });
                }
            });

            $recommend = [];

            foreach ($recommendedProducts as $id => $products) {
                if ($products) {
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

            $responseData['success'] = true;
            $responseData['result'] = $recommend;

        } else {
            $responseData['success'] = false;
        }

        return new JsonResponse($responseData);

    }

} 