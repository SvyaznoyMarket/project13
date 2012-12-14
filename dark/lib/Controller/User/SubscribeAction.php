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
                \App::$exception = null;
                \App::logger()->error($e);

                $error = 'Не удалось сохранить данные';
            }
        }

        $page = new \View\User\IndexPage();
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }
}