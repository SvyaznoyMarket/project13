<?php

namespace Controller\Enterprize;

class ConfirmPhoneAction {

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $repository = \RepositoryManager::enterprize();

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

        if ($this->isPhoneConfirmed()) {
            return (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
        }

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = $repository->getEntityByToken($enterprizeToken);

        if (!$enterpizeCoupon) {
            throw new \Exception\NotFoundException(sprintf('Купон @%s не найден.', $enterprizeToken));
        }

        $enterprizeDataDefault = [
            'name'            => null,
            'mobile'          => null,
            'email'           => null,
            'couponName'      => $enterpizeCoupon->getName(),
            'enterprizeToken' => $enterpizeCoupon->getToken(),
            'date'            => date('d.m.Y'),
            'time'            => date('H:i'),
            'enter_id'        => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
        ];
        $enterprizeData = array_merge($enterprizeDataDefault, array_intersect_key($data, $enterprizeDataDefault));

        $flash = $session->get('flash');
        $session->remove('flash');

        $page = new \View\Enterprize\ConfirmPhonePage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('error', !empty($flash['error']) ? $flash['error'] : null);
        $page->setParam('message', !empty($flash['message']) ? $flash['message'] : null);
        $page->setParam('enterprizeData', $enterprizeData);
        $page->setParam('viewParams', ['showSideBanner' => false]);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if ($this->isPhoneConfirmed()) {
            return (new \Controller\Enterprize\ConfirmEmailAction())->create($request);
        }

        $response = null;
        try {
            $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
            if (
                (!isset($data['mobile']) || empty($data['mobile']))
                && !($data['mobile'] = \App::user()->getEntity()->getMobilePhone())
            ) {
                throw new \Exception('Не получен мобильный телефон');
            }

            $result = \App::coreClientV2()->query(
                'confirm/mobile',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
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

        return new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show'));
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|void
     * @throws \Exception\NotFoundException
     */
    public function check(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);

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
                    'token'     => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
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

            $response = (new \Controller\Enterprize\CouponAction())->create($request);

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmPhone.show'));
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isPhoneConfirmed() {
        //\App::logger()->debug('Exec ' . __METHOD__);
        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        return isset($data['isPhoneConfirmed']) && $data['isPhoneConfirmed'] || (\App::user()->getEntity() ? \App::user()->getEntity()->getIsPhoneConfirmed() : false);
    }
}