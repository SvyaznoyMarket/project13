<?php

namespace Controller\Product;

class NotificationAction {

    const SUBSCRIPTION_EXISTS_CODE = 920;

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

            $emailValidator = new \Validator\Email();
            if (!$emailValidator->isValid($email)) {
                \App::logger()->error(['message' => 'Некорректный email', 'email' => $email], ['product.notification']);
                //throw new \Exception('Некорректный email');
            }

            $params = [
                'email'      => $email,
                'geo_id'     => $region->getId(),
                'product_id' => $product->getId(),
                'channel_id' => 2,
            ];
            if ($userEntity = \App::user()->getEntity()) {
                $params['token'] = $userEntity->getToken();
            }

            $exception = null;
            $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) use (&$exception) {
                $exception = $e;
                \App::exception()->remove($e);
            });

            // если отмечена галочка подписки на "Акции и суперпредложения"
            $subscribe = trim((string)$request->get('subscribe'));
            if ($subscribe === '1') {
                $params = [
                    'email'      => $email,
                    'geo_id'     => $region->getId(),
                    'channel_id' => 1,
                ];
                $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) use (&$exception) {
                    $exception = $e;
                    \App::exception()->remove($e);
                });
            }
            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], \App::config()->coreV2['retryCount']);

            if ($exception instanceof \Exception) {
                throw $exception;
            }

            $responseData = ['success' => true];
        } catch (\Exception $e) {
            if($e->getCode() == self::SUBSCRIPTION_EXISTS_CODE) {
                $responseData = ['success' => true];
            } else {
                \App::logger()->error($e);
                $responseData = [
                    'success' => false,
                    'error' => [
                        'code'    => $e->getCode(),
                        'message' => 'Не удалось создать подписку' . (\App::config()->debug ? sprintf(': %s', $e->getMessage()) : ''),
                    ],
                ];
            }
        }

        $response = new \Http\JsonResponse($responseData);

        // передаем email пользователя для RetailRocket
        if (true === $responseData['success'] && !empty($email)) {
            \App::retailrocket()->setUserEmail($response, $email);
        }

        return $response;
    }
}