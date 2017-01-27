<?php

namespace Controller\User;

use EnterApplication\CurlTrait;
use EnterApplication\Form;
use Session\AbTest\ABHelperTrait;
use Model\Session\FavouriteProduct;
use EnterQuery as Query;
use Model\Product\Entity as Product;
use Model\User\Entity as User;

class Action {

    use CurlTrait, ABHelperTrait;

    private $redirect;

    /**
     * @param \Http\Request $request
     * @return bool|\Http\JsonResponse|\Http\RedirectResponse
     */
    private function checkRedirect(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);
        $userEntity = null;
        $disposableParamName = \App::config()->authToken['disposableTokenParam'];

        $this->redirect = \App::router()->generateUrl(\App::config()->user['defaultRoute']); // default redirect to the /private page (Личный кабинет)
        $redirectTo = rawurldecode($request->get('redirect_to'));
        if ($redirectTo) {
            $this->redirect = $redirectTo;
        }
        if ($sessionRedirect = \App::session()->redirectUrl()) {
            parse_str(parse_url($sessionRedirect, PHP_URL_QUERY), $queryArr);
            if (array_key_exists($disposableParamName, $queryArr)) {
                $userEntity = $this->authWithToken($queryArr[$disposableParamName]);
                // удаляем токен из редиректа
                $sessionRedirect = str_replace(
                    sprintf('%s=%s', $disposableParamName, $queryArr[$disposableParamName]),
                    '',
                    $sessionRedirect
                );
            }
            $this->redirect = $sessionRedirect;
        }

        if (\App::user()->getEntity() || $userEntity) { // if user is logged in
            if (empty($this->redirect)) {
                $response = $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse([
                        'success'       => true,
                        'alreadyLogged' => true
                    ])
                    : new \Http\RedirectResponse(\App::router()->generateUrl(\App::config()->user['defaultRoute']));
                if ($userEntity) \App::user()->signIn($userEntity, $response);
                return $response;
            } else { // if redirect isset:
                $redirectUrl = $this->getRedirectUrlWithUserTokenParam($request);
                $response = $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse([
                        'success'       => true,
                        'alreadyLogged' => true,
                        'data'    => [
                            'link' => $redirectUrl,
                        ],
                    ])
                    : new \Http\RedirectResponse($redirectUrl);
                if ($userEntity) \App::user()->signIn($userEntity, $response);
                return $response;
            }
        }

        return false;
    }


    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function login(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();
        $response = null;

        $checkRedirect = $this->checkRedirect($request);
        if ($checkRedirect) {
            return $checkRedirect;
        }

        $formData = is_array($request->request->get('signin')) ? $request->request->get('signin') : [];

        $form = new Form\LoginForm();
        if ($request->isMethod('post')) {
            try {
                $form->fromArray($formData)->validate();

                if ($form->errors) {
                    throw new \Exception('Форма заполнена неправильно');
                }

                $authSource = null;
                $queryParams = [
                    'password' => $form->password->value,
                ];
                if (strpos($form->username->value, '@')) {
                    $queryParams['email'] = $form->username->value;
                    $authSource = 'email';
                }
                else {
                    $queryParams['mobile'] = $form->username->value;
                    $authSource = 'phone';
                }

                try {
                    $result = \App::coreClientV2()->query(
                        'user/auth',
                        $queryParams,
                        [],
                        \App::config()->coreV2['timeout'] * 2
                    );

                    $token = !empty($result['token']) ? $result['token'] : null;
                    if (!$token) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $userEntity = new \Model\User\Entity($result);
                    $userEntity->setToken($token);
                    \App::user()->setToken($token);
                    \App::user()->setEntity($userEntity);

                    // Запоминаем источник авторизации
                    $session->set('authSource', $authSource);

                    $redirectUrl = $this->getRedirectUrlWithUserTokenParam($request);
                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse([
                            'data'    => [
                                'user' => [
                                    'first_name'            => $userEntity->getFirstName(),
                                    'last_name'             => $userEntity->getLastName(),
                                    'mobile_phone'          => $userEntity->getMobilePhone(),
                                    'email'                 => $userEntity->getEmail(),
                                    'is_phone_confirmed'    => $userEntity->getIsPhoneConfirmed(),
                                    'is_email_confirmed'    => $userEntity->getIsEmailConfirmed(),
                                ],
                                'link' => $redirectUrl,
                            ],
                            'errors' => [],
                            'notice' => ['message' => 'Изменения успешно сохранены', 'type' => 'info'],
                        ])
                        : new \Http\RedirectResponse($redirectUrl);


                    \App::user()->signIn($userEntity, $response);

                    \App::user()->getCart()->pushStateEvent([]);

                    $this->syncUser($userEntity);

                    return $response;

                } catch(\Exception $e) {
                    \App::exception()->remove($e);
                    $session->redirectUrl($this->redirect);

                    $form->validateByError($e);
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $session->redirectUrl($this->redirect);
            }

            return
                $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'errors' => $form->errors,
                ])
                : new \Http\RedirectResponse(\App::router()->generateUrl('user.login'));
        } else {
            $page = new \View\User\LoginPage();
            $page->setParam('form', $form);
            $page->setParam('redirect_to', $this->redirect);
            $page->setParam('redirectUrlUserTokenParam', $this->getRedirectUrlUserTokenParam($request));
            $page->setParam('oauthEnabled', \App::config()->oauthEnabled);

            return new \Http\Response($page->show());
        }
    }

    /**
     * Авторизация с помощью одноразового токена
     *
     * @param string $token
     *
     * @return User|null
     */
    public function authWithToken($token = null)
    {
        $userEntity = null;
        $authResult = [];

        try {
            $authResult = \App::coreClientV2()->query(
                'user/auth-by-token',
                [],
                [
                    'token' => $token,
                    'client_id' => 'site',
                ]
            );
        } catch (\Exception $e) {
            // Если пользователь не найден по токену
            if ($e->getCode() === 614 ) {
                \App::exception()->remove($e);
            }
        }

        if (array_key_exists('token', $authResult)) {
            $userEntity = \RepositoryManager::user()->getEntityByToken($authResult['token']);
        }

        if ($userEntity) {
            $userEntity->setToken($authResult['token']);
            $this->syncUser($userEntity);
        }

        return $userEntity;
    }

    private function syncUser(\Model\User\Entity $userEntity) {
        $this->setFavourites();
        // объединение корзины
        $this->mergeUserCart($userEntity);
        // обновление региона в ядре
        $this->updateUserRegion();
    }

    /**
     * Объединение серверной корзины
     *
     * @param User $userEntity
     */
    private function mergeUserCart(User $userEntity)
    {
        if (!self::isCoreCart()) {
            return;
        }

        try {
            $mergeCartAction = new \EnterApplication\Action\Cart\Merge();
            $request = $mergeCartAction->createRequest();
            $request->userUi = $userEntity->getUi();
            $request->regionId = \App::user()->getRegion()->getId();

            $mergeCartAction->execute($request);
        } catch (\Exception $e) {
            \App::logger()->error(['message' => 'Не удалось объединить корзину пользователя', 'token' => \App::user()->getToken()], ['user']);
            \App::exception()->remove($e);
        }
    }

    /**
     * Обновление региона у пользователя
     */
    private function updateUserRegion()
    {
        try {
            \App::coreClientV2()->query(
                'user/update',
                ['token' => \App::user()->getToken()],
                [
                    'geo_id' => \App::user()->getRegion()->getId(),
                ]
            );
        } catch (\Exception $e) {
            \App::logger()->error(['message' => 'Не удалось обновить регион у пользователя', 'token' => \App::user()->getToken()], ['user']);
            \App::exception()->remove($e);
        }
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     */
    public function logout(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();

        $referer = $request->headers->get('referer');
        if (!$referer || $referer && preg_match('/(\/private\/)|(\/private$)/', $referer)) {
            $redirect_to = \App::router()->generateUrl('homepage');
        } else {
            $redirect_to = $referer;
        }

        if ($request->get('redirect_to')) {
            $redirect_to = $request->get('redirect_to');
            if (!preg_match('/^(\/|http).*/i', $redirect_to)) {
                $redirect_to = 'http://' . $redirect_to;
            }
        }

        $response = new \Http\RedirectResponse($redirect_to);

        $user->removeToken($response);

        // Очищаем источник авторизации
        \App::session()->remove('authSource');

        // SITE-1763
        $user->getCart()->clear();

        $this->removeFavourites();

        \App::user()->getCart()->pushStateEvent([]);

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function register(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $checkRedirect = $this->checkRedirect($request);
        if ($checkRedirect) {
            return $checkRedirect;
        }

        $formData = is_array($request->request->get('register')) ? $request->request->get('register') : [];

        $form = new Form\RegisterForm();
        if ($request->isMethod('post')) {
            try {
                $form->fromArray($formData)->validate();

                if ($form->errors) {
                    throw new \Exception('Форма заполнена неправильно');
                }

                $isSubscribe = true;
                $data = [
                    'first_name' => $form->firstName->value,
                    'geo_id'     => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null,
                ];

                if ($form->email->value) {
                    $data['email'] = $form->email->value;
                    $data['is_subscribe'] = $isSubscribe;
                }
                if ($phone = $form->phoneNumber->value) {
                    $phone = preg_replace('/^\+7/', '8', $phone);
                    $phone = preg_replace('/[^\d]/', '', $phone);
                    $data['mobile'] = $phone;
                    $data['is_sms_subscribe'] = $isSubscribe;
                }

                try {
                    $registerResult = \App::coreClientV2()->query('user/create', [], $data, 2 * \App::config()->coreV2['timeout']);
                    if (empty($registerResult['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $user = \RepositoryManager::user()->getEntityByToken($registerResult['token']);
                    if (!$user) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $registerResult['token']));
                    }
                    $user->setToken($registerResult['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse([
                            'success' => true,
                            'message' => sprintf('Пароль отправлен на ваш %s', !empty($data['email']) ? 'email' : 'телефон'),

                            'data'    => [
                                'link' => call_user_func(function() use($request) {
                                    $redirectUrl = $request->get('redirect_to');
                                    if ($redirectUrl && is_string($redirectUrl)) {
                                        $host = parse_url($redirectUrl, PHP_URL_HOST);

                                        if ($host === \App::config()->mainHost || $host === '') {
                                            return rawurldecode($redirectUrl);
                                        }
                                    }

                                    return null;
                                }),
                            ],
                            'newUser' => [
                                'id' => isset($registerResult['id']) ? $registerResult['id'] : '',
                            ],
                            'error' => null,
                            'notice' => ['message' => 'Изменения успешно сохранены', 'type' => 'info'],
                        ])
                        : new \Http\RedirectResponse($this->redirect);

                    //\App::user()->signIn($user, $response); // SITE-2279

                    try {
                        if ($request->request->get('loginAfterRegister')) {
                            $queryParams = [];

                            if (isset($registerResult['password'])) {
                                $queryParams['password'] = $registerResult['password'];
                            }

                            if (strpos($form->email->value, '@')) {
                                $queryParams['email'] = $form->email->value;
                                $authSource = 'email';
                            } else {
                                $queryParams['mobile'] = $form->phoneNumber->value;
                                $authSource = 'phone';
                            }

                            // Без вызова данного метода пользователь не станет участником EnterPrize (update: система EnterPrize подлежит удалению - FRONT-145)
                            $loginResult = \App::coreClientV2()->query(
                                'user/auth',
                                $queryParams,
                                [],
                                \App::config()->coreV2['timeout'] * 2
                            );

                            if (!empty($loginResult['token'])) {
                                $userEntity = new \Model\User\Entity($loginResult);
                                $userEntity->setToken($loginResult['token']);
                                \App::user()->setToken($loginResult['token']);
                                \App::user()->setEntity($userEntity);

                                \App::session()->set('authSource', $authSource);

                                \App::user()->signIn($userEntity, $response);
                                \App::user()->getCart()->pushStateEvent([]);
                                $this->syncUser($userEntity);
                            }
                        }
                    } catch(\Exception $e) {}

                    return $response;
                } catch(\Exception $e) {
                    \App::exception()->remove($e);

                    $form->validateByError($e);
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
            }

            $message = null;
            foreach ($form->errors as $i => $error) {
                if ('duplicate' === $error->code) {
                    $message = ['message' => $error->message . ' Хотите войти?', 'code' => 'duplicate', 'field' => $error->field];
                    unset($form->errors[$i]);
                }
            }

            if ($request->isXmlHttpRequest()) {
                $responseData = [
                    'errors' => $form->errors,
                    'notice' => $message ? $message : null,
                ];
                return new \Http\JsonResponse($responseData);
            }
        }

        $page = new \View\User\LoginPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function forgot(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $username = trim((string)$request->get('forgot')['username']);

        $errorMsg = null;
        $formErrors = [];

        $isEmail = strpos($username, '@');

        try {
            if (!$username) {
                $errorMsg = 'Не указан email или мобильный телефон';
                $formErrors[] = ['code' => 'invalid', 'message' => $errorMsg, 'field' => 'username'];
                throw new \Exception($errorMsg);
            }

            $result = \App::coreClientV2()->query('user/reset-password', [
                ($isEmail ? 'email' : 'mobile') => $username,
            ]);
            if (isset($result['confirmed']) && $result['confirmed']) {
                return new \Http\JsonResponse([
                    'error' => null,
                    'notice' => [
                        'message' => 'Новый пароль отправлен ' . ($isEmail ? "на {$username}" : 'по смс'),
                        'type'     => 'info'
                    ],
                ]);
            }
        } catch(\Exception $e) {
            \App::exception()->remove($e);

            switch ($e->getCode()) {
                case 600: case 601:
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Неправильный ' . ($isEmail ? 'email' : 'телефон или email'), 'field' => 'username'];
                    break;

                case 604: // Пользователь не найден
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Пользователь не зарегистрирован', 'field' => 'username'];
                    break;

                default:
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Не удалось запросить пароль. Попробуйте позже', 'field' => 'global'];
            }
        }

        return new \Http\JsonResponse([
            'errors' => $formErrors,
        ]);
    }

    /**
     * Сохранение избранных товаров в сессии
     */
    private function setFavourites() {
        $curl = $this->getCurl();

        $favoriteQuery = (new Query\User\Favorite\Get(\App::user()->getEntity()->getUi()))->prepare();

        $curl->execute();

        $products = [];
        foreach ($favoriteQuery->response->products as $item) {
            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $products[] = new \Model\Product\Entity(['ui' => $ui]);
        }

        \RepositoryManager::product()->prepareProductQueries($products);
        $curl->execute();

        // сохраняем продукты в сессию
        if ($products) {
            \App::session()->set(\App::config()->session['favouriteKey'],
                array_combine(
                    array_map(function (Product $product) {
                        return $product->getId();
                    }, $products),
                    array_map(function (Product $product) {
                        return (array)(new FavouriteProduct($product));
                    }, $products)
                )
            );
        }
    }

    /**
     * Удаление выбранных товаров из сессии
     */
    private function removeFavourites(){
        \App::session()->remove(\App::config()->session['favouriteKey']);
    }

    /**
     * @param \Http\Request $request
     * @return string
     */
    private function getRedirectUrlUserTokenParam(\Http\Request $request) {
        return (string)$request->get('redirect-url-user-token-param');
    }

    /**
     * @param \Http\Request $request
     * @return string
     */
    private function getRedirectUrlWithUserTokenParam(\Http\Request $request) {
        $url = $this->redirect;

        $redirectUrlUserTokenParam = $this->getRedirectUrlUserTokenParam($request);
        if ($redirectUrlUserTokenParam && preg_match('/(?:^|[a-z0-9\-]\.)my\.enter\.ru$/is', parse_url($url, PHP_URL_HOST))) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . urlencode($redirectUrlUserTokenParam) . '=' . urlencode(\App::user()->getEntity()->getToken());
        }

        return $url;
    }
}