<?php

namespace Controller\User;

class InfoAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

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
                $responseData['user']['link'] = \App::router()->generate(\App::config()->user['defaultRoute'] ?: 'user.orders');
                $responseData['user']['isEnterprizeMember'] = $user->getEntity()->isEnterprizeMember();
                $responseData['user']['isSubscribed'] = $user->getEntity()->getIsSubscribed();
                $responseData['user']['id'] = $userEntity->getId();
                $responseData['user']['email'] = $userEntity->getEmail();
                $responseData['user']['mobile'] = base64_encode($userEntity->getMobilePhone());
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

                $responseData['cartProducts'] = $cart->getProductsDumpNC();
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


    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function getSubscribeStatus(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

        $responseData = [];

        if (\App::config()->subscribe['enabled']) {
            $responseData['show']   = !$request->cookies->has(\App::config()->subscribe['cookieName']);
            $responseData['agreed'] = 1 == (int)$request->cookies->get(\App::config()->subscribe['cookieName']);
        }

        return new \Http\JsonResponse($responseData);
    }


    /**
     * @param \Http\Request $request
     * @param null $status
     * @return \Http\JsonResponse
     */
    public function setSubscribeStatus(\Http\Request $request, $status = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

        $cookie = null;

        try {
            if (\App::config()->subscribe['enabled']) {
                if (null !== $status && false != $status) {
                        $cookie = new \Http\Cookie(
                            \App::config()->subscribe['cookieName'],
                            (int)$status,
                            time() + (4 * 7 * 24 * 60 * 60),
                            '/',
                            null,
                            false,
                            false // важно httpOnly=false, чтобы js мог получить куку
                        );
                        $responseData['status'] = $status;
                }
                $responseData['success'] = true;
            }
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        $response = new \Http\JsonResponse($responseData);

        if (false == $status) {
            $domainParts = explode('.', \App::config()->mainHost);
            $tld = array_pop($domainParts);
            $domain = array_pop($domainParts);
            $subdomain = array_pop($domainParts);

            $response->headers->clearCookie(\App::config()->subscribe['cookieName'], '/', "$domain.$tld");
            $response->headers->clearCookie(\App::config()->subscribe['cookieName'], '/', "$subdomain.$domain.$tld");
        } elseif ($cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}