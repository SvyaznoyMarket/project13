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

        $page = new \View\Enterprize\ConfirmPhonePage();
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

            // обновление сессионной формы
//            $data['isPhoneConfirmed'] = true;
//            \App::session()->set(\App::config()->enterprize['formDataSessionKey'], $data);

//            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.create'));

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            \App::session()->set('flash', $e->getMessage());
//            $enterprizeToken = $request->get('enterprizeToken', null);
//            $response = $this->show($request, $enterprizeToken);
        }

        return $this->show($request, $request->get('enterprizeToken', null));
//        return $response;
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
    public function isPhoneConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);

        return isset($data['isPhoneConfirmed']) && $data['isPhoneConfirmed'] ? $data['isPhoneConfirmed'] : false;
    }
}