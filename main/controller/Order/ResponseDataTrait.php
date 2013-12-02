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
     * @param \Exception $exception
     * @param $responseData
     */
    protected function failResponseData(\Exception $exception, array &$responseData) {
        $router = \App::router();
        $cart = \App::user()->getCart();
        $region = \App::user()->getRegion();

        \App::exception()->remove($exception);

        if (!isset($responseData['paypalECS'])) {
            $responseData['paypalECS'] = false;
        }
        if (!isset($responseData['lifeGift'])) {
            $responseData['lifeGift'] = false;
        }
        if (!isset($responseData['oneClick'])) {
            $responseData['oneClick'] = false;
        }

        $productDataById = [];
        if ($exception instanceof \Curl\Exception) {
            $errorData = (array)$exception->getContent();
            if (isset($errorData['product_error_list'])) {
                $errorData =  (array)$errorData['product_error_list'];
            } else if (isset($errorData['detail']['product_error_list'])) {
                $errorData =  (array)$errorData['detail']['product_error_list'];
            } else {
                $errorData = [];
            }

            $quantitiesByProduct = [];
            foreach ($errorData as $errorItem) {
                if (!is_array($errorItem)) {
                    \App::logger()->error(['action' => __METHOD__, 'message' => 'Неверный формат ошибки', 'error.item' => $errorItem], ['order']);
                    continue;
                }
                $errorItem = array_merge(['code' => 0, 'message' => 'Неизвестная ошибка', 'id' => null], $errorItem);

                switch ($errorItem['code']) {
                    case 705: case 708:
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

            foreach (array_chunk(array_keys($productDataById), \App::config()->coreV2['chunk_size']) as $idsInChunk) {
                \RepositoryManager::product()->prepareCollectionById($idsInChunk, $region, function($data) use (&$productDataById, &$router, &$cart, &$quantitiesByProduct, &$responseData) {
                    foreach ($data as $item) {
                        if ($responseData['paypalECS']) {
                            $cartProduct = $cart->getPaypalProduct();
                            $setUrl = $router->generate('cart.paypal.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                            $deleteUrl = $router->generate('cart.paypal.product.delete', ['productId' => $item['id']]);
                        } else if ($responseData['lifeGift']) {
                            $cartProduct = \App::user()->getLifeGiftCart()->getProductById($item['id']);
                            $setUrl = $router->generate('cart.lifeGift.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                            $deleteUrl = $router->generate('cart.lifeGift.product.delete', ['productId' => $item['id']]);
                        } else if ($responseData['oneClick']) {
                            $cartProduct = \App::user()->getOneClickCart()->getProductById($item['id']);
                            $setUrl = $router->generate('cart.oneClick.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                            $deleteUrl = $router->generate('cart.oneClick.product.delete', ['productId' => $item['id']]);
                        } else {
                            $cartProduct = $cart->getProductById($item['id']);
                            $setUrl = $router->generate('cart.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                            $deleteUrl = $router->generate('cart.product.delete', ['productId' => $item['id']]);
                        }
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
                            'setUrl'     => $setUrl,
                            'deleteUrl'  => $deleteUrl,
                            'deliveries' => [],
                        ], $productDataById[$item['id']]);
                    }
                });
            }
            \App::coreClientV2()->execute();

            $responseData['products'] = $productDataById;

            if ((true === $responseData['paypalECS']) && !$cart->getPaypalProduct()) {
                $responseData['redirect'] = $router->generate('order.paypal.new');
            } else if ((true === $responseData['lifeGift']) && !(bool)\App::user()->getLifeGiftCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('order.lifeGift.new');
            } else if ((true === $responseData['oneClick']) && !(bool)\App::user()->getOneClickCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('order.oneClick.new');
            } else if ((false === $responseData['paypalECS']) && (false === $responseData['lifeGift']) && (false === $responseData['oneClick']) && $cart->isEmpty()) { // если корзина пустая, то редирект на страницу корзины
                $responseData['redirect'] = $router->generate('order');
            }
        }

        $message = null;

        // если ошибочные товары не найдены
        if (!(bool)$productDataById) {
            if ((true === $responseData['paypalECS']) && !$cart->getPaypalProduct()) {
                $responseData['redirect'] = $router->generate('cart');
                $message = 'Пустая корзина';
            } else if ((true === $responseData['lifeGift']) && !(bool)\App::user()->getLifeGiftCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('homepage');
                $message = 'Пустая корзина';
            } else if ((true === $responseData['oneClick']) && !(bool)\App::user()->getOneClickCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('homepage');
                $message = 'Пустая корзина';
            } else if ((false === $responseData['paypalECS']) && (false === $responseData['lifeGift']) && (false === $responseData['oneClick']) && $cart->isEmpty()) { // если корзина пустая, то редирект на страницу корзины
                $responseData['redirect'] = $router->generate('cart');
                $message = 'Пустая корзина';
            }
        }

        \App::logger()->error(['error' => ['code' => $exception->getCode(), 'message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]], ['order']);

        // приукрашиваем сообщение об ошибке
        if (!$message) {
            switch ($exception->getCode()) {
                case 705:
                    $message = 'Одного или нескольких товаров нет в наличии';
                    break;
                case 770:
                    $message = 'Невозможно расчитать доставку';
                    break;
                default:
                    $message = 'Ошибка формирования заказа';
                    break;
            }
        }

        if (isset($responseData['form']['error']) && (bool)$responseData['form']['error']) {
            $exception = new \Exception('Форма заполнена неверно', 0);
            unset($responseData['redirect']);
        } else {
            $exception = new \Exception($message, $exception->getCode());
        }

        $responseData['success'] = false;
        $responseData['error'] = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

        \App::logger()->error(['site.response' => $responseData], ['order']);
    }
}