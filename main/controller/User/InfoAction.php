<?php

namespace Controller\User;

class InfoAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

        try {
            $user = \App::user();
            $cart = $user->getCart();

            $actions = [];
            if (\App::config()->subscribe['enabled']) {
                $actions['subscribe'] = [
                    'show'   => !$request->cookies->has(\App::config()->subscribe['cookieName']),
                    'agreed' => 1 == (int)$request->cookies->get(\App::config()->subscribe['cookieName']),
                ];
            }

            $responseData = [
                'success' => true,
                'user'    => [
                    'name'         => null,
                    'isSubscribed' => null,
                    'link' => \App::router()->generate('user.login'),
                ],
                'cart'    => [
                    'sum'      => 0,
                    'quantity' => 0,
                    'product'  => [],
                    'service'  => [],
                ],
                'order'   => [
                    'hasCredit' => 1 == $request->cookies->get('credit_on'),
                ],
                'action'  => $actions,
            ];

            // если пользователь авторизован
            if ($userEntity = $user->getEntity()) {
                $responseData['user']['name'] = $userEntity->getName();
                $responseData['user']['link'] = \App::router()->generate('user');
                $responseData['user']['isSubscribed'] = $user->getEntity()->getIsSubscribed();
            }

            if (!$cart->isEmpty()) {
                $responseData['cart']['sum'] = $cart->getSum();
                $responseData['cart']['quantity'] = $cart->getProductsQuantity() + $cart->getServicesQuantity();

                // товары в корзине
                foreach ($cart->getProducts() as $cartProduct) {
                    $productData = [
                        'id'       => $cartProduct->getId(),
                        'price'    => $cartProduct->getPrice(),
                        'sum'      => $cartProduct->getSum(),
                        'quantity' => $cartProduct->getQuantity(),
                        'warranty' => [],
                        'service'  => [],
                    ];
                    foreach ($cartProduct->getWarranty() as $cartWarranty) {
                        $productData['warranty'][] = [
                            'id'       => $cartWarranty->getId(),
                            'price'    => $cartWarranty->getPrice(),
                            'sum'      => $cartWarranty->getSum(),
                            'quantity' => $cartWarranty->getQuantity(),
                        ];
                    }
                    foreach ($cartProduct->getService() as $cartService) {
                        $productData['warranty'][] = [
                            'id'       => $cartService->getId(),
                            'price'    => $cartService->getPrice(),
                            'sum'      => $cartService->getSum(),
                            'quantity' => $cartService->getQuantity(),
                        ];
                    }

                    $responseData['cart']['product'][] = $productData;
                }

                // услуги в корзине
                foreach ($cart->getServices() as $cartService) {
                    $responseData['cart']['service'][] = [
                        'id'       => $cartService->getId(),
                        'price'    => $cartService->getPrice(),
                        'sum'      => $cartService->getSum(),
                        'quantity' => $cartService->getQuantity(),
                    ];
                }
            }
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        return new \Http\JsonResponse($responseData);
    }
}