<?php

namespace Controller\Product;

use \Http\JsonResponse,
    \Http\Request,
    \Model\Product\Entity as ProductEntity;

class SmartChoiceAction {

    /** Возвращает рекомендации для продуктов
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(Request $request) {
        // request type: $.getJSON('/ajax/product-smartchoice',{"products":[55092,64686]},function(data){})

        \App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();
        $productIds = [];
        $recommendedIds = [];

        $products = $request->query->get('products');

        if ($products && is_array($products)) {

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

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$recommendedIds, $id) {
                    if ((bool)$data) {
                        $recommendedIds[$id] = $data;
                    }
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }

            \App::curl()->execute(null, 1);

            // Если для продуктов есть рекомендации
            if (count($recommendedIds)) {
                foreach ($recommendedIds as &$value) {
                    \RepositoryManager::product()->prepareCollectionById($value, $region, function ($data) use (&$value) {
                        if (!is_array($data)) return;

                        foreach ($data as $key => &$product) {
                            $product = new ProductEntity($product);
                            if (!$product instanceof ProductEntity) continue;
                            if ( !$product->getIsBuyable()) {
                                unset($data[$key]);
                            }
                        }
                        $value = $data;
                    });
                }
            }
            // Запрашиваем продукты
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            $recommend = [];

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

            $responseData['success'] = true;
            $responseData['result'] = $recommend;

        } else {
            $responseData['success'] = false;
        }

        return new JsonResponse($responseData);

    }

} 