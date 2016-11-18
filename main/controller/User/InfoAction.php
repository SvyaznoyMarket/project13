<?php

namespace Controller\User;

class InfoAction {
    use \EnterApplication\CurlTrait;

    /**
     * @param \Http\Request $request
     * @return array
     */
    public function getResponseData(\Http\Request $request) {
        $user = \App::user();

        $infoCookieName = \App::config()->user['infoCookieName'] ?: 'user_info';

        $responseData = [];
        try {
            $responseData = [
                'success' => true,
                'user'    => [
                    'isLogined'    => false,
                    'name'         => '',
                    'firstName'    => '',
                    'lastName'     => '',
                    'isSubscribed' => false,
                    'link'         => \App::router()->generateUrl('user.login'),
                    'id'           => '',
                    'email'        => '',
                    'mobile'       => '',
                    'isSubscribedToActionChannel' => false,
                    'countLoaded'  => false, // загружены ли количества заказов, избранных товаров и т.д.
                    'infoCookieName' => $infoCookieName,
                ],
                'cart'    => $user->getCart()->getDump(),
                'compare' => \App::session()->get(\App::config()->session['compareKey']),
                'order'   => [
                    'hasCredit' => 1 == $request->cookies->get('credit_on'),
                ],
                'action'  => [],
            ];

            // если пользователь авторизован
            /** @var \Model\User\Entity|null $userEntity */
            if ($userEntity = $user->getEntity()) {
                $responseData['user']['isLogined'] = true;
                $responseData['user']['name'] = $userEntity->getName();
                $responseData['user']['firstName'] = $userEntity->getFirstName();
                $responseData['user']['lastName'] = $userEntity->getLastName();
                $responseData['user']['link'] = \App::router()->generateUrl(\App::config()->user['defaultRoute'] ?: 'user.orders');
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

                $subscribeQuery = (new \EnterQuery\Subscribe\GetByUserToken($userEntity->getToken()))->prepare();
                $this->getCurl()->execute();

                if (is_array($subscribeQuery->response->subscribes)) {
                    foreach ($subscribeQuery->response->subscribes as $item) {
                        $subscribe = new \Model\Subscribe\Entity($item);
                        if (1 == $subscribe->getChannelId() && 'email' === $subscribe->getType() && $subscribe->getIsConfirmed()) {
                            $responseData['user']['isSubscribedToActionChannel'] = true;
                            break;
                        }
                    }
                }

                // SITE-6646
                call_user_func(function() use (&$request, &$responseData, &$infoCookieName) {
                    try {
                        $rawValue = $request->cookies->get($infoCookieName);
                        $value = json_decode($rawValue, true);
                        if (
                            is_array($value)
                            && isset($value['orderCount'])
                            && isset($value['favoriteCount'])
                            && isset($value['couponCount'])
                            && isset($value['subscribeCount'])
                            && isset($value['addressCount'])
                            && isset($value['messageCount'])
                        ) {
                            $responseData['user']['countLoaded'] = true;
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error($e);
                    }
                });
            }
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        return $responseData;
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