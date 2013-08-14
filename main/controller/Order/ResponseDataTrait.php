<?php

namespace Controller\Order;

trait ResponseDataTrait {
    /**
     * На основе ошибки модифицирует данные для ответа \Http\JsonResponse:
     * [
     *     'success' => false,
     *     'code'    => ['code' => $e->getCode(), 'message' => $e->getMessage()],
     * ]
     *
     * @param \Exception $e
     * @param $responseData
     */
    protected function failResponseData(\Exception $e, array &$responseData) {
        $router = \App::router();
        $cart = \App::user()->getCart();
        $region = \App::user()->getRegion();

        $productDataById = [];
        if ($e instanceof \Curl\Exception) {
            $errorData = (array)$e->getContent();
            $errorData = isset($errorData['product_error_list']) ? (array)$errorData['product_error_list'] : [];
            if ((bool)$errorData) {
                \App::exception()->remove($e);

                // приукрашиваем сообщение об ошибке
                switch ($e->getCode()) {
                    case 770:
                        $message = 'Невозможно расчитать доставку';
                        break;
                    default:
                        $message = 'Невозможно расчитать доставку';
                        break;
                }
                $e = new \Exception($message, $e->getCode());
            }

            $quantitiesByProduct = [];
            foreach ($errorData as $errorItem) {
                switch ($errorItem['code']) {
                    case 708:
                        $quantity = isset($errorItem['quantity_available']) ? $errorItem['quantity_available'] : 0;
                        $quantitiesByProduct[(int)$errorItem['id']] = $errorItem['quantity_available'];
                        $errorItem['message'] = !empty($quantity) ? sprintf('Доступно только %s шт.', $quantity) : $errorItem['message'];
                        break;
                    case 800:
                        $errorItem['message'] = 'Товар недоступен для продажи';
                        break;
                    default:
                        $errorItem['message'] = 'Товар не может быть заказан';
                        break;
                }

                $productDataById[$errorItem['id']] = [
                    'id'    => $errorItem['id'],
                    'error' => ['code' => $errorItem['code'], 'message' => $errorItem['message']],
                ];
            }

            foreach (array_chunk(array_keys($productDataById), 50) as $idsInChunk) {
                \RepositoryManager::product()->prepareCollectionById($idsInChunk, $region, function($data) use (&$productDataById, &$router, &$cart, &$quantitiesByProduct) {
                    foreach ($data as $item) {
                        $cartProduct = $cart->getProductById($item['id']);
                        if (!$cartProduct) {
                            \App::logger()->error(sprintf('Товар #%s не найден в корзине', $item['id']), ['order']);
                            continue;
                        }
                        $productDataById[$item['id']] = array_merge([
                            'id'         => (string)$item['id'],
                            'name'       => $item['name'],
                            'price'      => (int)$item['price'],
                            'sum'        => $cartProduct->getSum(),
                            'quantity'   => $cartProduct->getQuantity(),
                            'stock'      => (int)$item['stock'],
                            'image'      => $item['media_image'],
                            'url'        => $item['link'],
                            'setUrl'     => $router->generate('cart.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]),
                            'deleteUrl'  => $router->generate('cart.product.delete', ['productId' => $item['id']]),
                            'deliveries' => [],
                        ], $productDataById[$item['id']]);
                    }
                });
            }
            \App::coreClientV2()->execute();

            $responseData['products'] = $productDataById;
            $responseData['redirect'] = $router->generate('order');
        }

        // если ошибочные товары не найдены
        if (!(bool)$productDataById) {
            if ($cart->isEmpty()) { // если корзина пустая, то редирект на страницу корзины
                $responseData['redirect'] = $router->generate('cart');
            } else {
                if (1 == $cart->getProductsQuantity()) { // если в корзине всего один товар, то предлагаем попробовать заказ в один клик
                    $cartProducts = $cart->getProducts();
                    $cartProduct = reset($cartProducts);
                    $product = $cartProduct ? \RepositoryManager::product()->getEntityById($cartProduct->getId()) : null;
                    if ($product) {
                        $responseData['redirect'] = $product->getLink() . '#one-click';
                    }
                }
            }
        }

        $responseData['success'] = false;
        $responseData['error'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
    }
}