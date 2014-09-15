<?php

namespace Controller\Product;

class NotificationAction {

    const CHANNEL_2_SUBSCRIPTION_EXISTS_CODE = 920;
    const CHANNEL_1_SUBSCRIPTION_EXISTS_CODE = 910;
    const WRONG_EMAIL_CODE = 850;

    /**
     * @param $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function lowerPrice($productId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $client = \App::coreClientV2();
        $region = \App::user()->getRegion();

        $product = \RepositoryManager::product()->getEntityById($productId, $region);
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар #%s не найден', $productId));
        }

        $email = null;
        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $userEntity = \App::user()->getEntity();

            $params = [
                'email'      => $email,
                'geo_id'     => $region->getId(),
                'product_id' => $product->getId(),
                'channel_id' => 2,
            ];

            if ($userEntity) {
                $params['token'] = $userEntity->getToken();
            }

            $client->addQuery('subscribe/create', $params, [], null, function(\Curl\Exception $e){
                if ($e->getCode() == self::CHANNEL_2_SUBSCRIPTION_EXISTS_CODE) {
                    \App::exception()->remove($e);
                } else {
                    throw $e;
                }
            });

            // если отмечена галочка подписки на "Акции и суперпредложения"
            if (trim((string)$request->get('subscribe')) === '1') {
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

                    $client->addQuery('subscribe/create', $params, [], null, function(\Curl\Exception $e){
                        if ($e->getCode() == self::CHANNEL_1_SUBSCRIPTION_EXISTS_CODE) {
                            \App::exception()->remove($e);
                        } else {
                            throw $e;
                        }
                    });
                }
            }

            $client->execute();

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