<?php

namespace Controller\Subscribe;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $responseData = ['success' => false];
        $channelId = (int)$request->get('channel', 1);

        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $channels = \RepositoryManager::subscribeChannel()->getCollection(\App::user()->getEntity());
            if (!(bool)$channels) {
                throw new \Exception('Не получен ни один канал для подписки');
            }

            $params = [
                'email'      => $email,
                'channel_id' => $channelId,
            ];
            /* SITE-1374
            if ($userEntity = \App::user()->getEntity()) {
                $params['token'] = $userEntity->getToken();
            }
            */

            $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) {
                \App::logger()->error($e);
                \App::exception()->remove($e);
            });
            $client->execute(\App::config()->coreV2['retryTimeout']['huge']);
    
            $responseData = ['success' => true];
        } catch (\Exception $e) {
            \App::logger()->error($e);
            $responseData = ['success' => false];
        }

        return new \Http\JsonResponse($responseData);
    }

    /**
     * @return \Http\JsonResponse
     */
    public function cancel() {
        try {
            $responseData = ['success' => true];
        } catch (\Exception $e) {
            $responseData = ['success' => false];
        }

        return new \Http\JsonResponse($responseData);
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

        return $response;
    }
}