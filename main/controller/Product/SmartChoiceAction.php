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
                $product = \RepositoryManager::product()->getEntityById($id);
                if (!$product) {
                    \App::logger()->error(sprintf('Товар #%s не найден', $id), ['SmartChoice']);
                } else {
                    $productIds[] = $product->getId();
                }
            }

            // Запрашиваем рекомендации для существующих продуктов
            foreach ($productIds as $id) {
                $queryUrl = "{$rrConfig['apiUrl']}Recomendation/UpSellItemToItems/{$rrConfig['account']}/$id";

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$recommendedProducts, $id) {
                    if ((bool)$data) {
                        $recommendedProducts[$id] = $data;
                    }
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }

            \App::curl()->execute(null, 1);

            // Если для продуктов есть рекомендации
            if ($recommendedProducts) {
                $medias = [];

                foreach ($recommendedProducts as &$value) {
                    \RepositoryManager::product()->prepareCollectionById($value, $region, function ($data) use (&$value) {
                        if (!is_array($data)) return;

                        foreach ($data as $key => &$product) {
                            $product = new \Model\Product\Entity($product);
                            if (!$product->getIsBuyable()) {
                                unset($data[$key]);
                            }
                        }

                        $value = $data;
                    });

                    \RepositoryManager::product()->prepareProductsMediasByIds($value, $medias);
                }

                // Запрашиваем продукты
                \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

                foreach ($recommendedProducts as $value) {
                    \RepositoryManager::product()->setMediasForProducts($value, $medias);
                }
            }

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