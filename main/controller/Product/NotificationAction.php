<?php

namespace Controller\Product;

class NotificationAction {

    const SUBSCRIPTION_PRODUCT_EXISTS_CODE = 920;
    const SUBSCRIPTION_EXISTS_CODE = 910;
    const SUBSCRIPTION_NOT_EXISTS_CODE = 921;
    const WRONG_EMAIL_CODE = 850;

    /**
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function lowerPrice($productId, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $client = \App::coreClientV2();
        $region = \App::user()->getRegion();

        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['id' => $productId])];
        \RepositoryManager::product()->prepareProductQueries($products, '', $region);
        \App::coreClientV2()->execute();

        if (!$products) {
            throw new \Exception\NotFoundException(sprintf('Товар #%s не найден', $productId));
        }

        $email = null;
        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $userEntity = \App::user()->getEntity();

            $exception = null;

            if ($userEntity) {
                $client->addQuery(
                    'subscribe/bind-email-to-user',
                    ['token' => $userEntity->getToken(), 'email' => $email, 'channel_id' => 2],
                    [],
                    null,
                    function(\Curl\Exception $e) use(&$exception) {
                        \App::exception()->remove($e);
                        if ($e->getCode() != self::SUBSCRIPTION_NOT_EXISTS_CODE) {
                            $exception = $e;
                        }
                    }
                );
            }

            $params = [
                'email'      => $email,
                'geo_id'     => $region->getId(),
                'product_id' => $productId,
                'channel_id' => 2,
            ];

            if ($userEntity) {
                $params['token'] = $userEntity->getToken();
            }

            $client->addQuery('subscribe/create', $params, [], null, function(\Curl\Exception $e) use(&$exception) {
                \App::exception()->remove($e);
                if ($e->getCode() != self::SUBSCRIPTION_PRODUCT_EXISTS_CODE) {
                    $exception = $e;
                }
            });

            // если отмечена галочка подписки на "Акции и суперпредложения"
            if (trim((string)$request->get('subscribe')) === '1') {
                if ($userEntity) {
                    $client->addQuery(
                        'subscribe/bind-email-to-user',
                        ['token' => $userEntity->getToken(), 'email' => $email, 'channel_id' => 1],
                        [],
                        null,
                        function(\Curl\Exception $e) use(&$exception) {
                            \App::exception()->remove($e);
                            if ($e->getCode() != self::SUBSCRIPTION_NOT_EXISTS_CODE) {
                                $exception = $e;
                            }
                        }
                    );
                }

                if ($userEntity && $email === $userEntity->getEmail() && $userEntity->getIsEmailConfirmed()) {
                    $client->addQuery(
                        'subscribe/set',
                        ['token' => $userEntity->getToken()],
                        [
                            [
                                'is_confirmed' => true,
                                'channel_id' => 1,
                                'type' => 'email',
                                'email' => $email,
                            ]
                        ]
                    );
                } else {
                    $params = [
                        'email'      => $email,
                        'geo_id'     => $region->getId(),
                        'channel_id' => 1,
                    ];

                    if ($userEntity) {
                        $params['token'] = $userEntity->getToken();
                    }

                    $client->addQuery('subscribe/create', $params, [], null, function(\Curl\Exception $e) use(&$exception) {
                        \App::exception()->remove($e);
                        if ($e->getCode() != self::SUBSCRIPTION_EXISTS_CODE) {
                            $exception = $e;
                        }
                    });
                }
            }

            $client->execute();

            if ($exception && $exception instanceof \Exception) {
                throw $exception;
            }

            $responseData = ['success' => true];
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);

            switch ($e->getCode()) {
                case self::WRONG_EMAIL_CODE:
                    $message = 'Неправильный email';
                    break;
                default:
                    $message = 'Не удалось создать подписку' . (\App::config()->debug ? sprintf(': %s', $e->getMessage()) : '');
            }

            $responseData = [
                'success' => false,
                'error' => [
                    'code'    => $e->getCode(),
                    'message' => $message,
                ],
            ];
        }

        $response = new \Http\JsonResponse($responseData);

        // передаем email пользователя для RetailRocket
        if (true === $responseData['success'] && !empty($email)) {
            \App::retailrocket()->setUserEmail($response, $email);
        }

        return $response;
    }
}