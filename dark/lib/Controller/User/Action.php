<?php

namespace Controller\User;

class Action {
    public function login(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::user()->getEntity()) {
            return new \Http\RedirectResponse(\App::router()->generate('user'));
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
                    $form->setError('global', 'Неверно указан логин или пароль');
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

    public function logout() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();

        $user->removeToken();

        $response = new \Http\RedirectResponse(\App::router()->generate('homepage'));
        $user->setCacheCookie($response);

        return $response;
    }

    public function register(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }

    public function registerCorporate(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }

    public function forgot(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }

    public function reset(\Http\Request $request) {

    }
}