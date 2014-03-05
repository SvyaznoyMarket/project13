<?php

namespace Controller\Enterprize;

class ConfirmEmailAction {

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

        if ($this->isEmailConfirmed()) {
            return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
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

        $page = new \View\Enterprize\ConfirmEmailPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('error', !empty($flash['error']) ? $flash['error'] : null);
        $page->setParam('message', !empty($flash['message']) ? $flash['message'] : null);

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
            return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
        }

        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

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

        return new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.show', ['enterprizeToken' => $enterprizeToken]));
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

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

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

            $confirmToken = $request->get('confirm_token');
            if (!$confirmToken) {
                throw new \Exception('Не получен token');
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
                    'code'     => $confirmToken,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

            // обновление сессионной формы
            $data = array_merge($data, ['isEmailConfirmed' => true]);
            $session->set($sessionName, $data);

            $response = (new \Controller\Enterprize\Coupon())->create($request);

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

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.show', ['enterprizeToken' => $enterprizeToken]));
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isEmailConfirmed() {
        \App::logger()->debug('Exec ' . __METHOD__);
        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        return isset($data['isEmailConfirmed']) && $data['isEmailConfirmed'] ? $data['isEmailConfirmed'] : false;
    }
}