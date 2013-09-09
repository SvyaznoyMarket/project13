<?php

namespace Controller\Cart\Paypal;

class ProductAction {
    /**
     * @param int           $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     * @throws \Exception
     */
    public function set($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $cart = \App::user()->getCart();

        $productId = (int)$productId;
        $quantity = (int)$request->get('quantity', 1);

        /** @var $product \Model\Product\Entity|null */
        $product = null;

        $responseData = [];
        try {
            if ($quantity < 0) {
                $quantity = 0;
                \App::logger()->warn(sprintf('Указано неверное количество товаров. Запрос %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
            }

            if (!$productId) {
                throw new \Exception('Не получен ид товара');
            }

            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $cartProduct = new \Model\Cart\Product\Entity([
                'id'       => $product->getId(),
                'price'    => $product->getPrice(),
                'quantity' => $quantity,
                'sum'      => $product->getPrice() * $quantity,
            ]);

            $cart->setPaypalProduct($cartProduct);

            // crossss
            if (false && \App::config()->crossss['enabled'] && ($quantity > 0)) {
                (new \Controller\Crossss\CartAction())->product($product);
            }

            $productInfo = [
                'id'    =>  $product->getId(),
                'name'  =>  $product->getName(),
                'img'   =>  $product->getImageUrl(2),
                'link'  =>  $product->getLink(),
                'price' =>  $product->getPrice(),
            ];
            if (false && \App::config()->kissmentrics['enabled']) {
                try {
                    $kissInfo = \Kissmetrics\Manager::getCartEvent($product);
                    $productInfo = array_merge($productInfo, $kissInfo['product']);
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['kissmetrics']);
                }
            }

            $result = \App::coreClientV2()->query(
                'payment/paypal-set-checkout',
                [
                    'geo_id' => \App::user()->getRegion()->getId(),
                ],
                [
                    'amount'          => $cartProduct->getSum(),
                    'delivery_amount' => 0,
                    'currency'        => 'USD',
                    'return_url'      => \App::router()->generate('order.paypal.new', [], true),
                    'product'         => [
                        [
                            'id'       => $cartProduct->getId(),
                            'quantity' => $cartProduct->getQuantity(),
                        ],
                    ],
                    'service'         => [],
                ]
            );
            \App::logger()->info(['core.response' => $result], ['order', 'paypal']);

            if (empty($result['payment_url'])) {
                throw new \Exception('Не получен урл для редиректа');
            }

            $createdOrder = new \Model\Order\CreatedEntity($result);
            \App::logger()->info(['paymentUrl' => $createdOrder->getPaymentUrl()], ['order', 'paypal']);

            $parentCategoryId = $product->getParentCategory() ? $product->getParentCategory()->getId() : null;

            $responseData['success']  = true;
            $responseData['redirect'] = $createdOrder->getPaymentUrl();
            $responseData['cart']     = [
                'sum'           => $cartProduct ? $cartProduct->getSum() : 0,
                'quantity'      => $quantity,
                'full_quantity' => $cart->getProductsQuantity() + $cart->getServicesQuantity() + $cart->getWarrantiesQuantity(),
                'full_price'    => $cart->getSum(),
                'old_price'     => $cart->getOriginalSum(),
                'link'          => $product->getLink(),
                'order'         => [
                    'number' => $createdOrder->getNumber(),
                    'sum'    => $createdOrder->getSum(),
                    'paySum' => $createdOrder->getPaySum(),
                ],
                'category_id' => $parentCategoryId,
            ];
            $responseData['product'] = $productInfo;
        } catch (\Exception $e) {
            \App::logger()->error($e, ['order', 'paypal']);
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse($responseData)
            : new \Http\RedirectResponse($request->headers->get('referer') ?: (($product && $product->getLink()) ? $product->getLink() : \App::router()->generate('homepage')));
    }

    /**
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function delete(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $request->query->set('quantity', 0);

        return $this->set($productId, $request);
    }
}