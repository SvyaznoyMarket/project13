<?php

namespace Controller\Subscribe;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $userEntity = \App::user()->getEntity();
        $channelId = (int)$request->get('channel', 1);

        $email = null;
        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $controller = new \EnterApplication\Action\Subscribe\Create();
            $controllerRequest = $controller->createRequest();
            $controllerRequest->userToken = $userEntity ? $userEntity->getToken() : null;
            $controllerRequest->channelId = $channelId;
            $controllerRequest->email = $email;

            $controllerResponse = $controller->execute($controllerRequest);

            if ($error = $controllerResponse->errors->reset()) {
                throw new \Exception($error->message, $error->code);
            }

            $responseData = [
                'success' => true,
                'data'    => 'Спасибо! подтверждение подписки отправлено на указанный e-mail',
            ];
        } catch (\Exception $e) {
            \App::logger()->error($e);

            $responseData = [
                'success'   => false,
                'code'      => $e->getCode(),
                'error'     => $e->getMessage()
            ];

            if (910 == $e->getCode()) {
                $responseData['data'] = trim((string)$request->get('error_msg')) ?: 'Вы уже подписаны на нашу рассылку. Мы сообщим Вам о лучших скидках в письме. Не забывайте проверять почту от Enter!';
                $responseData['error'] = '';
            }

            if (850 == $e->getCode()) {
                $responseData['error'] = 'Вы ввели некорректный email';
            }
        }

        $response = new \Http\JsonResponse($responseData);

        // передаем email пользователя для RetailRocket
        if (true === $responseData['success'] && !empty($email)) {
            \App::retailrocket()->setUserEmail($response, $email);
        }

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function confirm(\Http\Request $request) {
        $client = \App::coreClientV2();

        $action = null;
        $email = null;
        $hasbro = null;
        $error = null;

        try {
            $token = $request->get('confirm_token');
            if (!$token) {
                throw new \Exception('Не получен токен подтверждения подписки');
            }

            $email = $request->get('email');
            if (!$email) {
                throw new \Exception('Не получен email подтверждения подписки');
            }

            $hasbro = $request->get('hasbro');

            $client->addQuery('subscribe/use-token', ['token' => $token], [],
                function($data) use (&$action) {
                    if (isset($data['action'])) {
                        $action = (string)$data['action'];
                    }
                },
                function (\Exception $e) use (&$error) {
                    $error = [
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                    \App::exception()->remove($e);
                }
            );
            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], \App::config()->coreV2['retryCount']);
        } catch (\Exception $e) {
            $error = [
                'code' => $e->getCode(),
                'message'  =>  $e->getMessage()
             ];
            \App::logger()->error($e);
        }

        if (empty($error)) {
            if (empty($email)) $error = ['message' => 'Не получен емейл пользователя'];
                elseif (empty($action)) $error = ['message' => 'Не получен ожидаемый ответа ядра'];
        }

        // 910 - код дубликата, если email уже подписан на этот канал рассылок
        /*if (!empty($error) && 910 != $error['code'] ) {
            $page = new \View\Subscribe\ConfirmPage();
            $page->setParam('action', $action);
            $page->setParam('error', $error);
            $page->setParam('email', $email);

            return new \Http\Response($page->show());
        }*/

        $redirectToken = 'subscribe_friends';
        if (1 == $hasbro) {
            $redirectToken = 'hasbro_email_confirm';

            if (!empty($error['code']) && 910 == $error['code']) {
                $redirectToken = 'hasbro_email_confirm_repeat';
            }
        }

        $response = new \Http\RedirectResponse(
            \App::router()->generate('content', ['token' => $redirectToken, 'email' => $email], true)
        );

        $promo = $request->get('promo', null);
        if ('enter_prize' == $promo) {
            $response = (new \Controller\Enterprize\ConfirmEmailAction())->check($request);
        }

        // передаем email пользователя для RetailRocket
        if (empty($error) && !empty($email)) {
            \App::retailrocket()->setUserEmail($response, $email);
        }

        return $response;
    }
}
