<?php

namespace Controller\User;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function login(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::user()->getEntity()) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(['success' => true])
                : new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        $redirect = (false !== strpos($request->get('redirect_to'), \App::config()->mainHost))
            ? $request->get('redirect_to')
            : \App::router()->generate('user');

        $form = new \View\User\LoginForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->request->get('signin'));
            if (!$form->getUsername()) {
                $form->setError('username', 'Не указан логин');
            }
            if (!$form->getPassword()) {
                $form->setError('password', 'Не указан пароль');
            }

            if ($form->isValid()) {
                $params = ['password' => $form->getPassword()];
                if (strpos($form->getUsername(), '@')) {
                    $params['email'] = $form->getUsername();
                }
                else {
                    $params['mobile'] = $form->getUsername();
                }

                try {
                    $result = [];
                    \App::coreClientV2()->addQuery(
                        'user/auth',
                        $params,
                        [],
                        function($data) use(&$result) {
                            $result = $data;
                        },
                        function(\Exception $e) {
                            \App::exception()->remove($e);
                        }
                    );
                    \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium'], \App::config()->coreV2['retryCount']);
                    if (empty($result['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $userEntity = \RepositoryManager::user()->getEntityByToken($result['token']);
                    if (!$userEntity) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                    }
                    $userEntity->setToken($result['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse([
                            'success' => true,
                            'data'    => [
                                'content' => \App::templating()->render('form-login', [
                                    'page'    => new \View\Layout(),
                                    'form'    => $form,
                                    'request' => \App::request(),
                                ]),
                                'user' => [
                                    'first_name'   => $userEntity->getFirstName(),
                                    'last_name'    => $userEntity->getLastName(),
                                    'mobile_phone' => $userEntity->getMobilePhone(),
                                ],
                                'link' => $redirect,
                            ],
                        ])
                        : new \Http\RedirectResponse($redirect);

                    \App::user()->signIn($userEntity, $response);

                    try {
                        \App::coreClientV2()->query('user/update', ['token' => \App::user()->getToken()], [
                            'geo_id' => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null,
                        ]);
                    } catch (\Exception $e) {
                        \App::logger()->error(sprintf('Не удалось обновить регион у пользователя token=%s', \App::user()->getToken()), ['user']);
                    }

                    return $response;
                } catch(\Exception $e) {
                    $form->setError('global', 'Неверно указан логин или пароль' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                }
            }

            // xhr
            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse([
                    'success' => $form->isValid(),
                    'data'    => [
                        'content' => \App::templating()->render('form-login', [
                            'page'    => new \View\Layout(),
                            'form'    => $form,
                            'request' => \App::request(),
                        ]),
                    ],
                ]);
            }
        }

        $page = new \View\User\LoginPage();
        $page->setParam('form', $form);
        $page->setParam('redirect', $redirect);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     */
    public function logout(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();

        $referer = $request->headers->get('referer');
        if(!$referer || $referer && preg_match('/(\/private\/)|(\/private$)/', $referer)) {
            $redirect_to = \App::router()->generate('homepage');
        } else {
            $redirect_to = $referer;
        }

        if ($request->get('redirect_to')) {
            $redirect_to = $request->get('redirect_to');
            if(!preg_match('/^(\/|http).*/i', $redirect_to)) {
                $redirect_to = 'http://' . $redirect_to;
            }
        }

        $response = new \Http\RedirectResponse($redirect_to); 

        $user->removeToken($response);
        $user->setCacheCookie($response);

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function register(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::user()->getEntity()) {
            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse(['success' => true])
                : new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        $form = new \View\User\RegistrationForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->request->get('register'));
            if (!$form->getFirstName()) {
                $form->setError('first_name', 'Не указано имя');
            }
            if (!$form->getUsername()) {
                $form->setError('username', 'Не указан номер телефона или email');
            }

            if ($form->isValid()) {
                $data = [
                    'first_name' => $form->getFirstName(),
                    'geo_id'     => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null,
                ];

                $isSubscribe = (bool)$request->get('subscribe', false);

                if (strpos($form->getUsername(), '@')) {
                    $data['email'] = $form->getUsername();
                    $data['is_subscribe'] = $isSubscribe;
                }
                else {
                    $phone = $form->getUsername();
                    $phone = preg_replace('/^\+7/', '8', $phone);
                    $phone = preg_replace('/[^\d]/', '', $phone);
                    $data['mobile'] = $phone;
                    $data['is_sms_subscribe'] = $isSubscribe;
                }

                try {
                    $result = \App::coreClientV2()->query('user/create', [], $data);
                    if (empty($result['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $user = \RepositoryManager::user()->getEntityByToken($result['token']);
                    if (!$user) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                    }
                    $user->setToken($result['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse([
                            'success' => true,
                            'data'    => [
                                'content' => \App::templating()->render('form-register', [
                                    'page'    => new \View\Layout(),
                                    'form'    => $form,
                                    'request' => \App::request(),
                                ]),
                            ],
                        ])
                        : new \Http\RedirectResponse(\App::router()->generate('user'));

                    \App::user()->signIn($user, $response);

                    return $response;
                } catch(\Exception $e) {
                    \App::exception()->remove($e);
                    $errorMess = $e->getMessage();
                    switch ($e->getCode()) {
                        case 686:
                        case 684:
                        case 689:
                        case 690:
                            $form->setError('username', $errorMess );
                            break;
                        case 609:
                        default:
                            $form->setError('global', 'Не удалось создать пользователя' . (\App::config()->debug ? (': ' . $errorMess) : '') );
                            break;
                    }
                }
            }

            // xhr
            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse([
                    'success' => $form->isValid(),
                    'data'    => [
                        'content' => \App::templating()->render('form-register', [
                            'page'    => new \View\Layout(),
                            'form'    => $form,
                            'request' => \App::request(),
                        ]),
                    ],
                ]);
            }
        }

        $page = new \View\User\LoginPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function registerCorporate(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::user()->getEntity() && $request->isXmlHttpRequest() ) {
            return new \Http\JsonResponse(['success' => true]);
        }


        \App::logger()->info(['action' => __METHOD__, 'request.request' => $request->request->all()], ['user']);

        $content = null;
        \App::contentClient()->addQuery('reg_corp_user_cont', [],
            function($data) use (&$content) {
                if (!empty($data['content'])) {
                    $content = $data['content'];
                }
            },
            function(\Exception $e) {
                \App::logger()->error(sprintf('Не получено содержимое для промо-страницы %s', \App::request()->getRequestUri()));
                \App::exception()->add($e);
            }
        );
        \App::contentClient()->execute();

        $form = new \View\User\CorporateRegistrationForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->get('register'));
            if (!$form->getFirstName()) {
                $form->setError('first_name', 'Не указано имя');
            }
            if (!$form->getFirstName()) {
                $form->setError('first_name', 'Укажите имя');
            }
            if (!$form->getMiddleName()) {
                $form->setError('middle_name', 'Укажите отчество');
            }
            if (!$form->getLastName()) {
                $form->setError('last_name', 'Укажите фамилию');
            }
            if (!$form->getEmail()) {
                $form->setError('email', 'Укажите email');
            }
            if (!$form->getPhone()) {
                $form->setError('phone', 'Укажите номер телефона');
            }
            if (!$form->getCorpName()) {
                $form->setError('corp_name', 'Укажите название организации');
            }
            if (!$form->getCorpLegalAddress()) {
                $form->setError('corp_legal_address', 'Укажите юридический адрес');
            }
            if (!$form->getCorpRealAddress()) {
                $form->setError('corp_real_address', 'Укажите фактический адрес');
            }
            if (!$form->getCorpINN()) {
                $form->setError('corp_inn', 'Укажите ИНН');
            }
            /*if (!$form->getCorpKPP()) {
                $form->setError('corp_kpp', 'Укажите КПП');
            }*/
            if (!$form->getCorpAccount()) {
                $form->setError('corp_account', 'Укажите расчетный счет');
            }
            if (!$form->getCorpKorrAccount()) {
                $form->setError('corp_korr_account', 'Укажите корреспондентский счет');
            }
            if (!$form->getCorpBIK()) {
                $form->setError('corp_bik', 'Укажите БИК');
            }
            if (!$form->getCorpOKPO()) {
                $form->setError('corp_okpo', 'Укажите ОКПО');
            }

            if ($form->isValid()) {
                $phone = $form->getPhone();
                $phone = preg_replace('/^\+7/', '8', $phone);
                $phone = preg_replace('/[^\d]/', '', $phone);

                $data = [
                    'first_name'    => $form->getFirstName(),
                    'last_name'     => $form->getLastName(),
                    'middle_name'   => $form->getMiddleName(),
                    'sex'           => 0,
                    //'birthday'      => null,
                    'email'         => $form->getEmail(),
                    'phone'         => null,
                    'mobile'        => $phone,
                    'is_subscribe'  => (bool)$request->get('subscribe', false),
                    'occupation'    => null,
                    'detail'        => [
                        //'legal_type' => null,
                        //'name'          => $form->getCorpName(),
                        'legal_type'    => 'ЮЛ',
                        'name_full'     =>
                            trim($form->getCorpForm() . ' ' . '«' . trim(preg_replace('/[^\d\w\- ]/ui', '', $form->getCorpName())) . '»'),
                        'address_legal' => $form->getCorpLegalAddress(),
                        'address_real'  => $form->getCorpRealAddress(),
                        'okpo'          => $form->getCorpOKPO(),
                        'inn'           => $form->getCorpINN(),
                        'kpp'           => $form->getCorpKPP(),
                        'bik'           => $form->getCorpBIK(),
                        'account'       => $form->getCorpAccount(),
                        'korr_account'  => $form->getCorpKorrAccount(),
                    ],
                ];

                try {
                    $result = \App::coreClientV2()->query('user/create-legal', [
                        'geo_id' => \App::user()->getRegion()->getId(),
                    ], $data);
                    \App::logger()->info(['core.response' => $result], ['user']);
                    if (empty($result['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $user = \RepositoryManager::user()->getEntityByToken($result['token']);
                    if (!$user) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                    }
                    $user->setToken($result['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse([
                            'success' => true,
                            'data'    => [
                                'content' => \App::templating()->render('form-registerCorporate', [
                                    'page'    => new \View\Layout(),
                                    'form'    => $form,
                                    'content' => $content,
                                    'request' => \App::request(),
                                ]),
                            ],
                        ])
                        : new \Http\RedirectResponse(\App::router()->generate('user'));

                    \App::user()->signIn($user, $response);

                    return $response;
                } catch(\Exception $e) {
                    \App::exception()->remove($e);
                    switch ($e->getCode()) {
                        case 686:
                            $form->setError('phone', 'Такой номер телефона уже зарегистрирован.');
                            break;
                        case 684:
                            $form->setError('email', 'Такой email уже зарегистрирован.');
                            break;
                        case 689:
                            $form->setError('email', 'Поле заполнено неверно.');
                            break;
                        case 690:
                            $form->setError('phone', 'Поле заполнено неверно.');
                            break;
                        case 692:
                            $form->setError('corp_inn', 'Поле заполнено неверно.');
                            break;
                        case 693:
                            $form->setError('corp_kpp', 'Поле заполнено неверно.');
                            break;
                        case 696:
                            $form->setError('corp_account', 'Поле заполнено неверно.');
                            break;
                        case 697:
                            $form->setError('corp_korr_account', 'Поле заполнено неверно.');
                            break;
                        case 694:
                            $form->setError('corp_bik', 'Поле заполнено неверно.');
                            break;
                        case 695:
                            $form->setError('corp_okpo', 'Поле заполнено неверно.');
                            break;
                        case 698:
                            $form->setError('corp_inn', 'Пользователь с таким ИНН уже зарегистрирован. Пожалуйста обратитесь в контакт-cENTER.');
                            break;
                        default:
                            $form->setError('global', 'Не удалось создать пользователя' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                            break;
                    }
                }
            }

            // xhr
            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse([
                    'success' => $form->isValid(),
                    'data'    => [
                        'content' => \App::templating()->render('form-registerCorporate', [
                            'page'    => new \View\Layout(),
                            'form'    => $form,
                            'content' => $content,
                            'request' => \App::request(),
                        ]),
                    ],
                ]);
            }
        }

        // список рутовых категорий
        $rootCategories = \RepositoryManager::productCategory()->getRootCollection();

        $page = new \View\User\CorporateRegistrationPage();
        $page->setParam('form', $form);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('content', $content);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function forgot(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $username = trim((string)$request->get('login'));

        $error = null;
        try {
            if (!$username) {
                $error = 'Не указан email или мобильный телефон';
                throw new \Exception($error);
            }

            $result = \App::coreClientV2()->query('user/reset-password', [
                (strpos($username, '@') ? 'email' : 'mobile') => $username,
            ]);
            if (isset($result['confirmed']) && $result['confirmed']) {
                return new \Http\JsonResponse(['success' => true]);
            }
        } catch(\Exception $e) {
            \App::exception()->remove($e);

            $error = $error ?: ('Не удалось запросить пароль. Попробуйте позже' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
        }

        return new \Http\JsonResponse(['success' => false, 'error' => $error]);
    }

    public function reset(\Http\Request $request) {

    }
}