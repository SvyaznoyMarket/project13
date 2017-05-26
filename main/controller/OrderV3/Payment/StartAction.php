<?php

namespace Controller\OrderV3\Payment;

use EnterApplication\CurlTrait;

class StartAction {
    use CurlTrait;

    /**
     * @param $request \Http\Request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute($orderAccessToken, $paymentMethodId, \Http\Request $request) {
        try {
            $privateClient = \App::coreClientPrivate();

            /** @var \Model\Order\Entity|null $order */
            $order = \RepositoryManager::order()->getEntityByAccessToken($orderAccessToken);

            if (!$order) {
                throw new \Exception('Заказ не получен');
            }

            $result = $privateClient->query('site-integration/payment-config',
                [
                    'method_id' => $paymentMethodId,
                    'order_id' => $order->id,
                ],
                [
                    'email' => $order->getUser() ? $order->getUser()->getEmail() : '',
                    'user_token' => $request->cookies->get('UserTicket'), // токен кросс-авторизации. может быть передан для Связного-Клуба (UserTicket)
                    'from'  => \App::config()->mainHost,
                ],
                2 * \App::config()->coreV2['timeout']
            );

            if (!$result) {
                throw new \Exception('Ошибка получения данных payment-config');
            }

            $validationResult = \App::coreClient()->query('payment/robokassa-check-order',
                $result['detail'],
                [],
                2 * \App::config()->coreV2['timeout']
            );

            return new \Http\JsonResponse(['success' => (bool)$validationResult['success']]);
        } catch (\Exception $e) {
            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['order']);
            return new \Http\JsonResponse(['success' => false]);
        }
    }
}