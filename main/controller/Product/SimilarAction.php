<?php

namespace Controller\Product;

class SimilarAction extends BasicRecommendedAction {

    /**
     * Разводящий метод: запускает либо smartengineClient , либо retailrocketClient
     *
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($productId, \Http\Request $request)
    {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            if (\App::config()->crossss['enabled']) {
                (new \Controller\Crossss\ProductAction())->recommended($request, $productId);
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $ABtestOption = \App::abTest()->getOption('test');
            $ABtest = reset($ABtestOption); /* @var $ABtest \Model\Abtest\Entity */

            if ( !empty( $ABtest ) ) { /// if

                $title = $ABtest->getName();
                $key = $ABtest->getKey();

                if ('retailrocket' == $key) {
                    $products = $this->getProductsFromRetailrocket($product, $request, 'ItemToItems');
                }else {
                    $products = $this->getProductsFromSmartengine($product, $request, 'relateditems');
                }

                if ($products instanceof \Http\JsonResponse) { // it is error
                    $response = $products->getContent();
                    return new \Http\JsonResponse([
                        $response
                        //json_decode($response)
                    ]);
                }

                if ( !isset($products) || !is_array($products) ) { // it is error
                    return new \Http\JsonResponse([
                        'success' => false,
                        'error' => ['code' => '404', 'message' => 'Not found products data in response. Method: ' . $key],
                    ]);
                }

                return new \Http\JsonResponse([ // it is SUCCESS!
                    'success' => true,
                    'content' => \App::closureTemplating()->render('product/__slider', [
                        'title' => $title,
                        'products' => $products,
                    ]),
                ]);

            }/// if

            return new \Http\JsonResponse([
                'success' => false,
                'error' => ['code' => '404', 'message' => 'Not found data in config'],
            ]);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['SimilarAction']);
            return $this->error($e);
        }

    }

}