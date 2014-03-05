<?php

namespace Controller\Enterprize;

class ConfirmPhoneAction {

    /**
     * @param \Http\Request $request
     * @param null $enterprizeToken
     * @return \Http\RedirectResponse|\Http\Response
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
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $flash = $session->get('flash');
        $session->remove('flash');

        $data = array_merge($session->get($sessionName, []), ['enterprizeToken' => $enterprizeToken]);
        $session->set($sessionName, $data);

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
        $page->setParam('error', !empty($flash['error']) ? $flash['error'] : null);
        $page->setParam('message', !empty($flash['message']) ? $flash['message'] : null);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if ($this->isPhoneConfirmed()) {
            return (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
        }

        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        $response = null;
        try {
            if (!isset($data['mobile']) || empty($data['mobile'])) {
                throw new \Exception('Не получен мобильный телефон');
            }

            $result = \App::coreClientV2()->query(
                'confirm/mobile',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => isset($data['token']) ? $data['token'] : \App::user()->getToken(),
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

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);
        }

        return new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show', ['enterprizeToken' => $enterprizeToken]));
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|void
     * @throws \Exception\NotFoundException
     */
    public function check(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        $response = null;
        try {
            $code = $request->get('code', null);
            if (!(bool)$code) {
                throw new \Exception('Не получен код');
            }

            if (!isset($data['mobile']) || empty($data['mobile'])) {
                throw new \Exception('Не получен мобильный телефон');
            }

            $result = \App::coreClientV2()->query(
                'confirm/mobile',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => isset($data['token']) ? $data['token'] : \App::user()->getToken(),
                ],
                [
                    'mobile' => $data['mobile'],
                    'code'   => $code,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/mobile']);

            // обновление сессионной формы
            $data = array_merge($data, ['isPhoneConfirmed' => true]);
            $session->set($sessionName, $data);

            $response = (new \Controller\Enterprize\Coupon())->create($request);

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show', ['enterprizeToken' => $enterprizeToken]));
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isPhoneConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);
        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        return isset($data['isPhoneConfirmed']) && $data['isPhoneConfirmed'] ? $data['isPhoneConfirmed'] : false;
    }
}