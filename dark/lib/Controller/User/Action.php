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
                ? new \Http\JsonResponse(array('success' => true))
                : new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        $form = new \View\User\LoginForm();
        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('signin'));
            if (!$form->getUsername()) {
                $form->setError('username', 'Не указан логин');
            }
            if (!$form->getPassword()) {
                $form->setError('password', 'Не указан пароль');
            }

            if ($form->isValid()) {
                $params = array('password' => $form->getPassword());
                if (strpos($form->getUsername(), '@')) {
                    $params['email'] = $form->getUsername();
                }
                else {
                    $params['mobile'] = $form->getUsername();
                }

                try {
                    $result = \App::coreClientV2()->query('user/auth', $params);
                    if (empty($result['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $user = \RepositoryManager::getUser()->getEntityByToken($result['token']);
                    if (!$user) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                    }
                    $user->setToken($result['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse(array(
                            'success' => true,
                            'data'    => array(
                                'content' => \App::templating()->render('form-login', array(
                                    'page'    => new \View\Layout(),
                                    'form'    => $form,
                                    'request' => \App::request(),
                                )),
                            ),
                        ))
                        : new \Http\RedirectResponse(\App::router()->generate('user'));

                    \App::user()->signIn($user, $response);


                    return $response;
                } catch(\Exception $e) {
                    $form->setError('global', 'Неверно указан логин или пароль' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                }
            }

            // xhr
            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse(array(
                    'success' => $form->isValid(),
                    'data'    => array(
                        'content' => \App::templating()->render('form-login', array(
                            'page'    => new \View\Layout(),
                            'form'    => $form,
                            'request' => \App::request(),
                        )),
                    ),
                ));
            }
        }

        $page = new \View\User\LoginPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @return \Http\RedirectResponse
     */
    public function logout() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();

        $user->removeToken();

        $response = new \Http\RedirectResponse(\App::router()->generate('user.login'));
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
                ? new \Http\JsonResponse(array('success' => true))
                : new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        $form = new \View\User\RegistrationForm();
        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('register'));
            if (!$form->getFirstName()) {
                $form->setError('first_name', 'Не указано имя');
            }
            if (!$form->getUsername()) {
                $form->setError('username', 'Не указан номер телефона или email');
            }

            if ($form->isValid()) {
                $data = array('first_name' => $form->getFirstName());
                if (strpos($form->getUsername(), '@')) {
                    $data['email'] = $form->getUsername();
                }
                else {
                    $phone = $form->getUsername();
                    $phone = preg_replace('/^\+7/', '8', $phone);
                    $phone = preg_replace('/[^\d]/', '', $phone);
                    $data['mobile'] = $phone;
                }

                try {
                    $result = \App::coreClientV2()->query('user/create', array(), $data);
                    if (empty($result['token'])) {
                        throw new \Exception('Не удалось получить токен');
                    }

                    $user = \RepositoryManager::getUser()->getEntityByToken($result['token']);
                    if (!$user) {
                        throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                    }
                    $user->setToken($result['token']);

                    $response = $request->isXmlHttpRequest()
                        ? new \Http\JsonResponse(array(
                            'success' => true,
                            'data'    => array(
                                'content' => \App::templating()->render('form-register', array(
                                    'page'    => new \View\Layout(),
                                    'form'    => $form,
                                    'request' => \App::request(),
                                )),
                            ),
                        ))
                        : new \Http\RedirectResponse(\App::router()->generate('user'));

                    \App::user()->signIn($user, $response);

                    return $response;
                } catch(\Exception $e) {
                    switch ($e->getCode()) {
                        case 684:
                        case 686:
                            $form->setError('username', 'Такой пользователь уже зарегистрирован.');
                            break;
                        case 609:
                        default:
                            $form->setError('global', 'Не удалось создать пользователя' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                            break;
                    }
                }
            }

            // xhr
            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse(array(
                    'success' => $form->isValid(),
                    'data'    => array(
                        'content' => \App::templating()->render('form-register', array(
                            'page'    => new \View\Layout(),
                            'form'    => $form,
                            'request' => \App::request(),
                        )),
                    ),
                ));
            }
        }

        $page = new \View\User\LoginPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    public function registerCorporate(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }

    public function forgot(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $username = trim((string)$request->get('login'));

        $error = null;
        try {
            if (!$username) {
                $error = 'Не указан email или мобильный телефон';
                throw new \Exception($error);
            }

            $result = \App::coreClientV2()->query('user/reset-password', array(
                (strpos($username, '@') ? 'email' : 'mobile') => $username,
            ));
            if (isset($result['confirmed']) && $result['confirmed']) {
                return new \Http\JsonResponse(array('success' => true));
            }
        } catch(\Exception $e) {
            $error = $error ?: ('Не удалось запросить пароль. Попробуйте позже' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
        }

        return new \Http\JsonResponse(array('success' => false, 'error' => $error));
    }

    public function reset(\Http\Request $request) {

    }
}