<?php

namespace Controller\Subscribe;

use Http\RedirectResponse;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $response = new \Http\JsonResponse(['success' => false]);

        try {
            $email = trim((string)$request->get('email'));
            if (empty($email)) {
                throw new \Exception('Не передан email для подписки');
            }

            $channels = \RepositoryManager::subscribeChannel()->getCollection(\App::user()->getEntity());
            if (!(bool)$channels) {
                throw new \Exception('Не получен ни один канал для подписки');
            }

            foreach ($channels as $channel) {
                $params = [
                    'email'      => $email,
                    'channel_id' => $channel->getId(),
                ];
                if ($userEntity = \App::user()->getEntity()) {
                    $params['token'] = $userEntity->getToken();
                }

                $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) {
                    \App::logger()->error($e);
                    \App::exception()->remove($e);
                });
            }

            $client->execute(\App::config()->coreV2['retryTimeout']['huge'], \App::config()->coreV2['retryCount']);

            $cookie = new \Http\Cookie(
                \App::config()->subscribe['cookieName'],
                1,
                time() + 3 * 365 * 24 * 60 * 60,
                '/',
                null,
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
            $response = new \Http\JsonResponse(['success' => true]);
            $response->headers->setCookie($cookie);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        return $response;
    }

    /**
     * @return \Http\JsonResponse
     */
    public function cancel() {
        $response = new \Http\JsonResponse(['success' => false]);

        try {
            $cookie = new \Http\Cookie(
                \App::config()->subscribe['cookieName'],
                0,
                time() + 3 * 365 * 24 * 60 * 60,
                '/',
                null,
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
            $response = new \Http\JsonResponse(['success' => true]);
            $response->headers->setCookie($cookie);
        } catch (\Exception $e) {
            \App::logger()->error($e);
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
        try {
            $token = $request->get('IdSL');
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
                    \App::logger()->error($e);
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

        return new RedirectResponse(\App::router()->generate('content', ['token' => 'subscribe_friends'], true));
    }
}