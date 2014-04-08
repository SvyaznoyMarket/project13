<?php

namespace Controller\Enterprize;

class ConfirmEmailAction {

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

        if ($this->isEmailConfirmed()) {
            return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
        }

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

        $page = new \View\Enterprize\ConfirmEmailPage();
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
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if ($this->isEmailConfirmed()) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize.create'));
        }

        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        $response = null;
        try {
            if (!isset($data['email']) || empty($data['email'])) {
                throw new \Exception('Не получен email');
            }

            $result = \App::coreClientV2()->query(
                'confirm/email',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
                ],
                [
                    'email'    => $data['email'],
                    'template' => 'enter_prize',
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

            if ($request->get('isRepeatRending', false)) {
                \App::session()->set('flash', ['message' => 'Письмо повторно отправлено']);
            }

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);
        }

        return new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.show'));
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

        $response = null;
        try {
            $email = $request->get('email');
            if (!$email) {
                throw new \Exception('Не получен email');
            }

            $promo = $request->get('promo');
            if (!$promo) {
                throw new \Exception('Не получен promo');
            }

            $confirmCode = $request->get('code');
            if (!$confirmCode) {
                throw new \Exception('Не получен code');
            }

            $userToken = !empty($data['token']) ? $data['token'] : \App::user()->getToken();

            $result = \App::coreClientV2()->query(
                'confirm/email',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => $userToken,
                ],
                [
                    'email'    => $email,
                    'template' => $promo,
                    'code'     => $confirmCode,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

            // обновление сессионной формы
            $data = array_merge($data, ['isEmailConfirmed' => true]);
            $session->set($sessionName, $data);

            if ($userToken==null) {

                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.warn'));

            } else {

                $response = (new \Controller\Enterprize\CouponAction())->create($request);

                // авторизовываем пользователя
                if ($userToken && !\App::user()->getEntity()) {
                    $user = \RepositoryManager::user()->getEntityByToken($userToken);
                    if ($user) {
                        $user->setToken($userToken);
                        \App::user()->signIn($user, $response);
                    } else {
                        \App::logger()->error(sprintf('Не удалось получить пользователя по токену %s', $userToken));
                    }
                }
            }

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.show'));
        }

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|void
     * @throws \Exception\NotFoundException
     */
    public function warn(\Http\Request $request) {

        $response = null;

        $page = new \View\Enterprize\ConfirmEmailPageWarn();
        $page->setParam('enterpizeCoupon', null);
        $page->setParam('error', !empty($flash['error']) ? $flash['error'] : null);
        $page->setParam('message', !empty($flash['message']) ? $flash['message'] : null);
        $page->setParam('enterprizeData', null);
        $page->setParam('viewParams', ['showSideBanner' => false]);

        $response = new \Http\Response($page->show());

        return $response;
    }

    /**
     * @return bool
     */
    public function isEmailConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);
        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        $userToken = !empty($data['token']) ? $data['token'] : \App::user()->getToken();

        if (isset($data['email'])) {
            $status = \App::coreClientV2()->query(
                'confirm/status',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token' => $userToken,
                ],
                [
                    'criteria' => $data['email'],
                    'type' => 'email',
                ],
                \App::config()->coreV2['hugeTimeout']
            );
        }

        if (isset($status['is_confirmed'])) {
            $result = $status['is_confirmed'];
        } else {
            $result = false;
        }

        return $result;
    }
}