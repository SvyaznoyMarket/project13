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
                    \App::config()->session['cookie_domain'],
                    false,
                    false // важно httpOnly=false, чтобы js мог получить куку
                );
            }

            $responseData = [
                'success' => true,
                'user'    => [
                    'name'         => null,
                    'firstName'    => null,
                    'lastName'     => null,
                    'isSubscribed' => null,
                    'link' => \App::router()->generate('user.login'),
                    'id' =>  null,
                    'email' =>  null,
                ],
                'cart'    => [
                    'sum'      => 0,
                    'quantity' => 0,
                ],
                'compare'   => \App::session()->get(\App::config()->session['compareKey']),
                'order'   => [
                    'hasCredit' => 1 == $request->cookies->get('credit_on'),
                ],
                'action'  => [],
            ];

            // если пользователь авторизован
            /** @var \Model\User\Entity|null $userEntity */
            if ($userEntity = $user->getEntity()) {
                $responseData['user']['name'] = $userEntity->getName();
                $responseData['user']['firstName'] = $userEntity->getFirstName();
                $responseData['user']['lastName'] = $userEntity->getLastName();
                $responseData['user']['link'] = \App::router()->generate('user.orders');
                $responseData['user']['isEnterprizeMember'] = $user->getEntity()->isEnterprizeMember();
                $responseData['user']['isSubscribed'] = $user->getEntity()->getIsSubscribed();
                $responseData['user']['id'] = $userEntity->getId();
                $responseData['user']['email'] = $userEntity->getEmail();
                $responseData['user']['emailHash'] = md5($userEntity->getEmail());
                $responseData['user']['sex'] = $userEntity->getSex(); // 1-мужской, 2-женский

                // sclubNumber
                $sclubCard = $userEntity->getSclubCard() ?: [];
                $responseData['user']['sclubNumber'] = !empty($sclubCard) && isset($sclubCard['number']) ? $sclubCard['number'] : null;
            }

            if (!$cart->isEmpty()) {

                // заполнение недостающих данных для продуктов
                $productsToUpdate = [];
                $productsNC = $cart->getProductsNC();

                $responseData['cart']['sum'] = $cart->getSum();
                $responseData['cart']['quantity'] = $cart->getProductsQuantity();

                foreach ($productsNC as $id => $value) {
                    foreach (['name', 'price', 'url', 'image', 'category', 'rootCategory'] as $prop) {
                        if (!isset($value[$prop]) || empty($value[$prop])) {
                            $productsToUpdate[] = $id;
                            break;
                        }
                    }
                }

                foreach (\RepositoryManager::product()->getCollectionById($productsToUpdate) as $product) {
                    $cart->updateProductNC($product);
                }

                $cartProductData = [];
                foreach ($cart->getProductsNC() as $cartProduct) {

                    if (!$cartProduct) { // SITE-4400
                        \App::logger()->error(['Товар не найден', 'product' => ['id' => $cartProduct['id']], 'sender' => __FILE__ . ' ' .  __LINE__], ['cart']);

                        continue;
                    }

                    $cartProductData[] = [
                        'id'                => $cartProduct['id'],
                        'name'              => $cartProduct['name'],
                        'price'             => $cartProduct['price'],
                        'formattedPrice'    => $helper->formatPrice($cartProduct['price']),
                        'quantity'          => $cartProduct['quantity'],
                        'deleteUrl'         => $helper->url('cart.product.delete', ['productId' => $cartProduct['id']]),
                        'link'              => $cartProduct['url'],
                        'img'               => $cartProduct['image'],
                        'cartButton'        => [ 'id' => \View\Id::cartButtonForProduct($cartProduct['id']), ],
                        'category'          => $cartProduct['category'],
                        'rootCategory'      => $cartProduct['rootCategory'],
                    ];

                }

                $responseData['cartProducts'] = $cartProductData;
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