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
        $isSubscribeViaSms = (bool)$request->request->get('subscribe_sms', false);
        $error = null;
        if ($request->isMethod('post')) {

            try {
                $result = \App::coreClientV2()->query('user/update', ['token' => \App::user()->getToken()], [
                    'is_subscribe'      => $isSubscribe,
                    'is_sms_subscribe'  => $isSubscribeViaSms,
                ]);

                if (!isset($result['confirmed']) || !$result['confirmed']) {
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
}