<?php

namespace Controller\Product;

class NotificationAction {
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

        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $params = [
                'email'      => $email,
                'geo_id'     => $region->getId(),
                'channel_id' => 4,
            ];
            if ($userEntity = \App::user()->getEntity()) {
                $params['token'] = $userEntity->getToken();
            }

            $exception = null;
            $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) use (&$exception) {
                $exception = $e;
                \App::exception()->remove($e);
            });
            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], \App::config()->coreV2['retryCount']);

            if ($exception instanceof \Exception) {
                throw $exception;
            }

            $responseData = ['success' => true];
        } catch (\Exception $e) {
            \App::logger()->error($e);
            $responseData = [
                'success' => false,
                'error' => [
                    'code'    => $e->getCode(),
                    'message' => 'Не удалось создать подписку' . (\App::config()->debug ? sprintf(': %s', $e->getMessage()) : ''),
                ],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}