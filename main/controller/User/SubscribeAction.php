<?php

namespace Controller\User;

class SubscribeAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $isSubscribe = (bool)$request->request->get('subscribe', false);
        $error = null;
        if ($request->isMethod('post')) {

            try {
                $response = \App::coreClientV2()->query('user/update', array('token' => \App::user()->getToken()), array(
                    'is_subscribe'  => $isSubscribe,
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить данные');
                }

                return new \Http\RedirectResponse(\App::router()->generate('user'));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $error = 'Не удалось сохранить данные';
            }
        }

        $page = new \View\User\IndexPage();
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }

    public function addEmail($email, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        //TODO: отправляем данные в ядро
        $response = new \Http\JsonResponse(['success' => true,]);
        $cookie = new \Http\Cookie(
            'subscribed',
            true,
            time() + 3*365*24*60*60,
            '/',
            null,
            false,
            false // важно httpOnly=false, чтобы js мог получить куку
        );

        $request->headers->setCookie($cookie);
        return $response;
    }
}