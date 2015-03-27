<?php

namespace Controller\Order;

trait ResponseDataTrait {
    protected $cart;
    /** @var array */
    protected $productDataById;

    protected function getCart() {
        if (!(bool)$this->cart) {
            $this->cart = \App::user()->getCart();
        }
        return $this->cart;
    }

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

        // обработка исключения
        $this->exceptionHandler($exception, $responseData);

        if (isset($responseData['form']['error']) && (bool)$responseData['form']['error']) {
            $exception = new \Exception('Форма заполнена неверно', 0);
            $responseData['redirect'] = 0;
        } else {
            $exception = new \Exception($this->getErrorMessage($exception, $responseData), $exception->getCode());
        }

        $responseData['success'] = false;
        $responseData['error'] = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

        \App::logger()->error(['site.response' => $responseData], ['order']);
    }

    /**
     * @param $exception
     * @param $responseData
     */
    protected function exceptionHandler($exception, &$responseData) {
       $region = \App::user()->getRegion();
       $router = \App::router();
       $cart = $this->getCart();

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
                   $quantitiesByProduct[(int)$errorItem['id']] = $quantity;
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
                       if ($responseData['lifeGift']) {
                           $cartProduct = \App::user()->getLifeGiftCart()->getProductById($item['id']);
                           if (!$cartProduct) continue;
                           $setUrl = $router->generate('cart.lifeGift.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                           $deleteUrl = $router->generate('cart.lifeGift.product.delete', ['productId' => $item['id']]);
                       } else if ($responseData['oneClick']) {
                           $cartProduct = \App::user()->getOneClickCart()->getProductById($item['id']);
                           if (!$cartProduct) continue;
                           $setUrl = $router->generate('cart.oneClick.product.set', ['productId' => $item['id'], 'quantity' => isset($quantitiesByProduct[$cartProduct->getId()]) ? $quantitiesByProduct[$cartProduct->getId()] : $cartProduct->getQuantity()]);
                           $deleteUrl = $router->generate('cart.oneClick.product.delete', ['productId' => $item['id']]);
                       } else {
                           $cartProduct = $cart->getProductById($item['id']);
                           if (!$cartProduct) continue;
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
           $this->productDataById = $productDataById;

           if ((true === $responseData['paypalECS'])) {
               //$responseData['redirect'] = $router->generate('order.paypal.new'); // SITE-2729
           } else if ((true === $responseData['lifeGift'])) {
               //$responseData['redirect'] = $router->generate('order.lifeGift.new'); // SITE-2729
           } else if ((true === $responseData['oneClick'])) {
               //$responseData['redirect'] = $router->generate('order.oneClick.new'); // SITE-2777
           } else {
               //$responseData['redirect'] = $router->generate('order'); // SITE-2729
           }
       }
   }

    /**
     * @param $exception
     * @param $responseData
     * @return null|string
     */
    protected function getErrorMessage($exception, &$responseData){
        $message = null;

        // если ошибочные товары не найдены
        $this->errorProductNotFoundHandler($message, $responseData);
        \App::logger()->error(['error' => ['code' => $exception->getCode(), 'message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]], ['order']);

        // приукрашиваем сообщение об ошибке
        if (!$message) {
            switch ($exception->getCode()) {
                case 705:
                    $message = 'Одного или нескольких товаров нет в наличии';
                    break;
                case 759:
                    $message = 'Некорректный email';
                    break;
                case 770:
                    $message = 'Невозможно расчитать доставку';
                    break;
                case 729:
                    $message = 'Не указан банк для кредита. Выберите, пожалуйста, банк';
                    // это ошибка валидации, не будем перезагружать стр, дадим пользавателю выбрать-таки банк
                    $responseData['redirect'] = 0;
                    break;
                /*case 729: // можно перечислить коды ошибок с понятным юзеру описанием от ядра
                    $message = $exception->getMessage();
                    break;*/
                case 735:
                    $message = $exception->getMessage();
                    $responseData['redirect'] = 0;
                    break;
                case 720:
                    $message = 'Ошибка оформления заказа. Такой заказ уже существует';
                    break;
                default:
                    $message = 'Ошибка формирования заказа';
                    break;
            }
        }

        return $message;
    }

    /**
     * @param $message
     * @param $responseData
     */
    protected function errorProductNotFoundHandler(&$message, &$responseData) {
        $router = \App::router();
        $cart = $this->getCart();

        if (!(bool)$this->productDataById) {
            if ((true === $responseData['lifeGift']) && !(bool)\App::user()->getLifeGiftCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('homepage');
                $message = 'Пустая корзина';
            } else if ((true === $responseData['oneClick']) && !(bool)\App::user()->getOneClickCart()->getProducts()) {
                $responseData['redirect'] = $router->generate('homepage');
                $message = 'Пустая корзина';
            } else if ($cart->isEmpty()) { // если корзина пустая, то редирект на страницу корзины
                $responseData['redirect'] = $router->generate('cart');
                $message = 'Пустая корзина';
            } else {
                // Например, для ответа ядра с кодом code: 705 (Одного или нескольких товаров нет в наличии)
                $responseData['redirect'] = $router->generate('cart');
            }
        }
    }
}