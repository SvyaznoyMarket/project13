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

        $user = \App::user();
        /* @var $cart   \Session\Cart */
        $cart = $user->getCart();

        $helper = new \Helper\TemplateHelper();

        /** @var $cookies \Http\Cookie[] */
        $cookies = [];

        try {
            if (!$request->cookies->has('infScroll')) {
                $cookies[] = new \Http\Cookie(
                    'infScroll',
                    1,
                    time() + (4 * 7 * 24 * 60 * 60),
                    '/',
                    null,
                    false,
                    false // важно httpOnly=false, чтобы js мог получить куку
                );
            }

            $responseData = [
                'success' => true,
                'user'    => [
                    'name'         => null,
                    'isSubscribed' => null,
                    'link' => \App::router()->generate('user.login'),
                    'id' =>  null,
                    'email' =>  null
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
                $responseData['user']['id'] = $userEntity->getId();
                $responseData['user']['email'] = $userEntity->getEmail();
                $responseData['user']['emailHash'] = md5($userEntity->getEmail());
            }

            if (!$cart->isEmpty()) {
                $responseData['cart']['sum'] = $cart->getSum();
                $responseData['cart']['quantity'] = $cart->getProductsQuantity() + $cart->getServicesQuantity();

                $productsById = [];
                foreach (\RepositoryManager::product()->getCollectionById(array_keys($cart->getProducts())) as $product) {
                    $productsById[$product->getId()] = $product;
                }

                $cartProductData = [];
                foreach ($cart->getProducts() as $cartProduct) {
                    /* @var $product \Model\Product\Entity|null */
                    $product = isset($productsById[$cartProduct->getId()]) ? $productsById[$cartProduct->getId()] : null;

                    $cartProductData[] = [
                        'id'             => $cartProduct->getId(),
                        'name'           => $product ? $product->getName() : null,
                        'price'          => $cartProduct->getPrice(),
                        'formattedPrice' => $helper->formatPrice($cartProduct->getPrice()),
                        'quantity'       => $cartProduct->getQuantity(),
                        'deleteUrl'      => $helper->url('cart.product.delete', ['productId' => $cartProduct->getId()]),
                        'url'            => $product ? $product->getLink() : null,
                        'image'          => $product ? $product->getImageUrl() : null,
                        'cartButton'     => [
                            'id' => \View\Id::cartButtonForProduct($cartProduct->getId()),
                        ],
                    ];

                    /*
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
                    */
                }

                /*
                foreach ($cart->getServices() as $cartService) {
                    $buttons['service'][] = [
                        'id'       => \View\Id::cartButtonForService($cartService->getId()),
                        'quantity' => $cartService->getQuantity(),
                    ];
                }
                */

                $responseData['cartProducts'] = $cartProductData;
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

        $response = new \Http\JsonResponse($responseData);

        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}