<?php

namespace Controller\Enterprize;

class ConfirmEmailAction {

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

        if ($this->isEmailConfirmed()) {
            return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
        }

        $session = \App::session();
        $error = $session->get('flash');
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

        $page = new \View\Enterprize\ConfirmEmailPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('error', $error);

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

        if ($this->isEmailConfirmed()) {
            return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
        }

        $client = \App::coreClientV2();

        $response = null;
        try {
            $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
            $data['email'] = 'vitaly.shaposhnik@gmail.com';
            if (!isset($data['email']) || empty($data['email'])) {
                throw new \Exception('Не получен email');
            }

            $result = $client->query(
                'confirm/email',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => 'ADEE70AD-96D7-48DB-88E8-FAC2FDC27DAA',//\App::user()->getToken(),
                ],
                [
                    'email'    => $data['email'],
                    'template' => 'enter_prize',
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

            // обновление сессионной формы
            $data['isEmailConfirmed'] = true;
            \App::session()->set(\App::config()->enterprize['formDataSessionKey'], $data);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.create'));

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            \App::session()->set('flash', $e->getMessage());
            $enterprizeToken = $request->get('enterprizeToken', null);
            $response = $this->show($request, $enterprizeToken);
        }

        return $response;
    }

    /**
     * @param \Http\Request $request
     */
    public function check(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http');
        }

    }

    /**
     * @return bool
     */
    public function isEmailConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);

        return isset($data['isEmailConfirmed']) && $data['isEmailConfirmed'] ? $data['isEmailConfirmed'] : false;
    }
}