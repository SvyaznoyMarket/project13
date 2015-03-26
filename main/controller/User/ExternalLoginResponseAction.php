<?php

namespace Controller\User;

class ExternalLoginResponseAction {
    private $redirect;
    private $requestRedirect;

    /**
     * @param \Http\Request $request
     * @return bool|\Http\JsonResponse|\Http\RedirectResponse
     */
    private function checkRedirect(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $this->redirect = \App::router()->generate(\App::config()->user['defaultRoute']); // default redirect to the /private page (Личный кабинет)
        $redirectTo = (rawurldecode($request->get('redirect_to'))) ? rawurldecode($request->get('redirect_to')) : rawurldecode($request->headers->get('referer'));

        if ($redirectTo) {
            $this->redirect = $redirectTo;
            $this->requestRedirect = $redirectTo;
        }

        if (\App::user()->getEntity()) { // if user is logged in
            if (empty($redirectTo)) {
                return $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse(['success' => true])
                    : new \Http\RedirectResponse(\App::router()->generate(\App::config()->user['defaultRoute']));
            } else { // if redirect isset:
                return $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse([
                        'success' => true,
                        'data'    => [
                            'link' => $redirectTo,
                        ],
                    ])
                    : new \Http\RedirectResponse($redirectTo);
            }
        }

        return false;
    }


    public function execute($providerName, \Http\Request $request) {
        $checkRedirect = $this->checkRedirect($request);
        if ($checkRedirect) return $checkRedirect;

        $user = \App::user();

        try {
            $provider = \App::oauth($providerName);
        } catch (\Exception $e) {
            throw new \Exception\NotFoundException(sprintf('Не найден провайдер аутентификации "%s"', $providerName));
        }

        try {
            $profile = $provider->getUser($request);

            if (!$profile instanceof \Oauth\Model\EntityInterface) {
                throw new \Exception('Не получен профайл пользователя');
            }
            if (!$profile->getId()) {
                throw new \Exception('У профайла не установлен id');
            }

            //Если получили корректный профайл пользователя пробуем его авторизовать
            try
            {
                $params = [];
                if($profile instanceof \Oauth\Model\Facebook\Entity)
                {
                    $params['email'] = $profile->getEmail();
                    $params['fb_id'] = $profile->getId();
                    $params['fb_access_token'] = $profile->getAccessToken();
                }
                elseif($profile instanceof \Oauth\Model\Vkontakte\Entity){
                    $params['email'] = $profile->getEmail();
                    $params['vk_id'] = $profile->getId();
                    $params['vk_access_token'] = $profile->getAccessToken();
                }
                elseif($profile instanceof \Oauth\Model\Twitter\Entity){
                    //Отключен из-за невозможности получения email и телефона пользователя
                    $params['email'] = '';
                    $params['tw_id'] = $profile->getId();
                    $params['tw_access_token'] = $profile->getAccessToken();
                }
                elseif($profile instanceof \Oauth\Model\Odnoklassniki\Entity){
                    //Отключен из-за невозможности получения email и телефона пользователя
                    $params['email'] = '';
                    $params['od_id'] = $profile->getId();
                    $params['od_access_token'] = $profile->getAccessToken();
                }

                //Пытаемся авторизовать пользователя
                $authSource = 'email';
                $result = \App::coreClientV2()->query('user/social-auth',$params,[]);
                if (empty($result['token'])) {
                    throw new \Exception('Не удалось получить токен');
                }

                $userEntity = \RepositoryManager::user()->getEntityByToken($result['token']);

                if (!$userEntity) {
                    throw new \Exception(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                }

                $userEntity->setToken($result['token']);

                // Запоминаем источник авторизации
                \App::session()->set('authSource', $authSource);

                $response = $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse([
                        'data'    => [
                            'user' => [
                                'first_name'   => $userEntity->getFirstName(),
                                'last_name'    => $userEntity->getLastName(),
                                'mobile_phone' => $userEntity->getMobilePhone(),
                            ],
                            'link' => $this->redirect,
                        ],
                        'error' => null,
                        'notice' => ['message' => 'Изменения успешно сохранены', 'type' => 'info'],
                    ])
                    : new \Http\RedirectResponse($this->redirect);

                // передаем email пользователя для RetailRocket
                if ($userEntity->getEmail() != '') {
                    \App::retailrocket()->setUserEmail($response, $userEntity->getEmail());
                }

                \App::user()->signIn($userEntity, $response);

                try {
                    \App::coreClientV2()->query('user/update', ['token' => \App::user()->getToken()], [
                        'geo_id' => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null,
                    ]);
                } catch (\Exception $e) {
                    \App::logger()->error(sprintf('Не удалось обновить регион у пользователя token=%s', \App::user()->getToken()), ['user']);
                }

                return $response;

            } catch (\Exception $e) {

                //тут пытаемся создать пользователя
                try {
                    $data = [
                        'first_name'=>$profile->getFirstName(),
                        'last_name'=>$profile->getLastName(),
                        'sex'=>$profile->getSex(),
                        'birthday'=>$profile->getBirthday(),
                        'email'=>$profile->getEmail(),
                        'geo_id'     => \App::user()->getRegion() ? \App::user()->getRegion()->getId() : null
                    ];

                    $result = \App::coreClientV2()->query('user/create', [], $data, \App::config()->coreV2['hugeTimeout']);

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
                            'message' => sprintf('Пароль отправлен на ваш %s', !empty($data['email']) ? 'email' : 'телефон'),

                            'data'    => [
                                //'link' => $this->redirect,
                            ],
                            'error' => null,
                            'notice' => ['message' => 'Изменения успешно сохранены', 'type' => 'info'],
                        ])
                        : new \Http\RedirectResponse($this->redirect);

                    // передаем email пользователя для RetailRocket
                    if (isset($data['email']) && !empty($data['email'])) {
                        \App::retailrocket()->setUserEmail($response, $data['email']);
                    }

                    \App::user()->signIn($user, $response);

                    try {
                        \App::coreClientV2()->query('user/create-account', ['token' => \App::user()->getToken()], $params);
                    } catch (\Exception $e) {
                        \App::logger()->error(sprintf('Не удалось обновить Account пользователя token=%s', \App::user()->getToken()), ['user']);
                    }

                    if ($request->query->get('subscribe') === '1' && isset($data['email']) && $data['email'] != '') {
                        \App::coreClientV2()->query('subscribe/create', [
                            'email'      => $data['email'],
                            'geo_id'     => \App::user()->getRegion()->getId(),
                            'channel_id' => 1,
                            'token'      => \App::user()->getToken(),
                        ]);
                    }

                    return $response;
                } catch(\Exception $e) {
                    $page = new \View\User\ExternalLoginResponsePage();
                    $page->setParam('error', 'Неудачная попытка авторизации');

                    return new \Http\Response($page->show());
                }
            }

            //$response = new \Http\RedirectResponse(\App::router()->generate(\App::config()->user['defaultRoute']));
            //$user->signIn($user, $response);

            return $response;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $page = new \View\User\ExternalLoginResponsePage();
        $page->setParam('error', 'Неудачная попытка авторизации');

        return new \Http\Response($page->show());
    }
}
