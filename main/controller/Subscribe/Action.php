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
        $channelId = 1;

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
        try {
            $token = $request->get('confirm_token');
            if (!$token) {
                throw new \Exception('Не получен токен подтверждения подписки');
            }

            $client->addQuery('subscribe/use-token', ['token' => $token], [],
                function($data) use (&$action) {
                    if (isset($data['action'])) {
                        $action = (string)$data['action'];
                    }
                },
                function (\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], \App::config()->coreV2['retryCount']);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        /*
        $page = new \View\Subscribe\ConfirmPage();
        $page->setParam('action', $action);

        return new \Http\Response($page->show());
        */

        return new \Http\RedirectResponse(\App::router()->generate('content', ['token' => 'subscribe_friends'], true));
    }
}