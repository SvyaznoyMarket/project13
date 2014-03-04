<?php

namespace Controller\Enterprize;

class ConfirmPhoneAction {

    /**
     * @param \Http\Request $request
     * @param null $enterprizeToken
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show(\Http\Request $request, $enterprizeToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if (!$enterprizeToken) {
            throw new \Exception\NotFoundException();
        }

        if ($this->isPhoneConfirmed()) {
            return (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
        }

        $session = \App::session();
        $flash = $session->get('flash');
        $error = !empty($flash['error']) ? $flash['error'] : null;
        $message = !empty($flash['message']) ? $flash['message'] : null;
        $session->remove('flash');

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = null;
        if ($enterprizeToken) {
            \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupon, $enterprizeToken) {
                foreach ((array)$data as $item) {
                    if ($enterprizeToken == $item['token']) {
                        $enterpizeCoupon = new \Model\EnterprizeCoupon\Entity($item);
                    }
                }
            });
            \App::dataStoreClient()->execute();
        }

        $page = new \View\Enterprize\ConfirmPhonePage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('error', $error);
        $page->setParam('message', $message);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if ($this->isPhoneConfirmed()) {
            return (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
        }

        $client = \App::coreClientV2();

        $response = null;
        try {
            $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

            if (!isset($data['mobile']) || empty($data['mobile'])) {
                throw new \Exception('Не получен мобильный телефон');
            }

            $result = $client->query(
                'confirm/mobile',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => \App::user()->getToken(),
                ],
                [
                    'mobile' => $data['mobile'],
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/mobile']);

            if ($request->get('isRepeatRending', false)) {
                \App::session()->set('flash', ['message' => 'Повторно отправлен новый код подтверждения']);
            }

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);
        }

        return new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show', ['enterprizeToken' => $request->get('enterprizeToken', null)]));
    }

    /**
     * @param \Http\Request $request
     */
    public function check(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $client = \App::coreClientV2();

        $response = null;
        try {
            $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

            $code = $request->get('code', null);
            if (!(bool)$code) {
                throw new \Exception('Не получен код');
            }

            if (!isset($data['mobile']) || empty($data['mobile'])) {
                throw new \Exception('Не получен мобильный телефон');
            }

            $result = $client->query(
                'confirm/mobile',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => \App::user()->getToken(),
                ],
                [
                    'mobile' => $data['mobile'],
                    'code'   => $code,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/mobile']);

            // обновление сессионной формы
            $data['isPhoneConfirmed'] = true;
            \App::session()->set(\App::config()->enterprize['formDataSessionKey'], $data);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.create'));

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);
            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show', ['enterprizeToken' => $request->get('enterprizeToken', null)]));
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isPhoneConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);

        return isset($data['isPhoneConfirmed']) && $data['isPhoneConfirmed'] ? $data['isPhoneConfirmed'] : false;
    }
}