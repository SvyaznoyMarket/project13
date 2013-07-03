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
                ],
                'order'   => [
                    'hasCredit' => 1 == $request->cookies->get('credit_on'),
                ],
                'action'  => [],
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


                $buttons = [];
                foreach ($cart->getProducts() as $cartProduct) {
                    $buttons['product'][] = [
                        'id'       => \View\Id::cartButtonForProduct($cartProduct->getId()),
                        'quantity' => $cartProduct->getQuantity(),
                    ];

                    foreach ($cartProduct->getWarranty() as $cartWarranty) {
                        $buttons['warranty'][] = [
                            'id'       => \View\Id::cartButtonForProductWarranty($cartProduct->getId(), $cartWarranty->getId()),
                            'quantity' => $cartWarranty->getQuantity(),
                        ];
                    }
                    foreach ($cartProduct->getService() as $cartService) {
                        $buttons['service'][] = [
                            'id'       => \View\Id::cartButtonForProductService($cartProduct->getId(), $cartService->getId()),
                            'quantity' => $cartService->getQuantity(),
                        ];
                    }
                }

                foreach ($cart->getServices() as $cartService) {
                    $buttons['service'][] = [
                        'id'       => \View\Id::cartButtonForService($cartService->getId()),
                        'quantity' => $cartService->getQuantity(),
                    ];
                }

                $responseData['action']['cartButton'] = $buttons;
            }

            if (\App::config()->subscribe['enabled']) {
                $responseData['action']['subscribe'] = [
                    'show'   => !$request->cookies->has(\App::config()->subscribe['cookieName']),
                    'agreed' => 1 == (int)$request->cookies->get(\App::config()->subscribe['cookieName']),
                ];
            }
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        return new \Http\JsonResponse($responseData);
    }
}