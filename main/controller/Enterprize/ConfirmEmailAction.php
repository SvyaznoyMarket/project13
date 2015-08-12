<?php

namespace Controller\Enterprize;

class ConfirmEmailAction {

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
            return new \Http\RedirectResponse(\App::router()->generate('enterprize', $request->query->all())); // $request->query->all() нужен для SITE-5969
        }

        if ($this->isEmailConfirmed()) {
            //return (new \Controller\Enterprize\ConfirmPhoneAction())->create($request);
            return (new \Controller\Enterprize\CouponAction())->create($request);
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
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $userEntity = \App::user()->getEntity();

        if ($this->isEmailConfirmed()) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize.create'));
        }

        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        $response = null;
        try {
            if (!isset($data['email']) || empty($data['email'])) {
                throw new \Exception('Не получен email');
            }

            $userToken = !empty($data['token']) ? $data['token'] : \App::user()->getToken();

            $result = \App::coreClientV2()->query(
                'confirm/email',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => $userToken,
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

            // получаем $user_id
            $user_id = null;
            if ($userEntity) {
                $user_id = $userEntity->getId();
            } elseif (!empty($userToken)) {
                $user = \RepositoryManager::user()->getEntityByToken($userToken);
                if ($user) {
                    $user_id = $user->getId();
                }
            }

            // пишем данные формы в хранилище
            try {
                if (!isset($user_id) || empty($user_id)) {
                    throw new \Exception('Не передан user_id');
                }

                $storageResult = \App::coreClientPrivate()->query('storage/post', ['user_id' => $user_id], $data);

            } catch(\Exception $exception) {
                \App::exception()->remove($exception);
                \App::logger()->error($exception, ['enterprize', 'storage/post']);
            }

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);
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
        //\App::logger()->debug('Exec ' . __METHOD__);

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

            $result = \App::coreClientV2()->query(
                'confirm/email',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
                ],
                [
                    'email'    => $email,
                    'template' => $promo,
                    'code'     => $confirmCode,
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

            // получаем данные с хранилища
            try {
                if (!isset($result['user_id']) || empty($result['user_id'])) {
                    throw new \Exception('Не пришел user_id от ядра');
                }

                $storageResult = \App::coreClientPrivate()->query('storage/get', ['user_id' => $result['user_id']]);

                if (!(bool)$storageResult || !isset($storageResult['value'])) {
                    throw new \Exception(sprintf('Не пришли данные с хранилища для user_id=%s', $result['user_id']));
                }

                $storageData = (array)json_decode($storageResult['value'], true);

                // перелаживаем данные с хранилища в сессию
                foreach ($storageData as $name => $value) {
                    $data = array_merge($data, [$name => $value]);
                }

                // если в хранилище присутствует флаг Enterprize регистрации, оставляем его
                if (array_key_exists('isRegistration', $storageData) && (bool)$storageData['isRegistration']) {
                    //$storagePostResult = \App::coreClientPrivate()->query('storage/post', ['user_id' => $result['user_id']], ['isRegistration' => $storageData['isRegistration']]);
                    $storagePostResult = \App::coreClientPrivate()->query('storage/post', ['user_id' => $result['user_id']], array_merge($storageData, ['email' => $email]));
                // иначе чистим хранилище
                } else {
                    $delete = \App::coreClientPrivate()->query('storage/delete', ['user_id' => $result['user_id']]);
                }

            } catch(\Exception $exception) {
                \App::exception()->remove($exception);
                \App::logger()->error($exception, ['enterprize', 'storage/get', 'storage/delete']);
            }

            // обновление сессионной формы
            $data = array_merge($data, ['isEmailConfirmed' => true]);
            $session->set($sessionName, $data);

            $userToken = !empty($data['token']) ? $data['token'] : \App::user()->getToken();
            if ($userToken == null) {
                $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.warn'));
            } else {
                $response = (new \Controller\Enterprize\CouponAction())->create($request, $data);

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

            // передаем email пользователя для RetailRocket
            \App::retailrocket()->setUserEmail($response, $email);

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::session()->set('flash', ['error' => $e->getMessage()]);

            $response = new \Http\RedirectResponse(\App::router()->generate('enterprize.confirmEmail.show', $request->query->all())); // $request->query->all() нужен для SITE-5969
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
        //\App::logger()->debug('Exec ' . __METHOD__);
        $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);

        $userToken = !empty($data['token']) ? $data['token'] : \App::user()->getToken();

        $result = false;
        try {
            if (!isset($data['email']) || empty($data['email'])) {
                throw new \Exception('Не передан email');
            }

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

            if (isset($status['is_confirmed'])) {
                $result = $status['is_confirmed'];
            }

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e, ['enterprize', 'isEmailConfirmed']);
        }

        return $result;
    }
}