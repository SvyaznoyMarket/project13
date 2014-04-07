<?php

namespace Controller\Enterprize;

class FormAction {

    /**
     * @param null $enterprizeToken
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function show($enterprizeToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        if (!$enterprizeToken) {
            return new \Http\RedirectResponse(\App::router()->generate('enterprize'));
        }

        $user = \App::user();
        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        $session->set($sessionName, array_merge(
            [
                'isPhoneConfirmed' => false,
                'isEmailConfirmed' => false,
            ],
            $session->has($sessionName) ? $session->get($sessionName) : [],
            [
                'enterprizeToken' => $enterprizeToken,
            ]
        ));

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

        $data = (array)$session->get($sessionName, []);

        // если пользователь авторизован и уже является участником enterprize
        if ($user->getEntity() && $user->getEntity()->isEnterprizeMember()) {
            $data = array_merge($data, [
                'token'            => $user->getToken(),
                'name'             => $user->getEntity()->getFirstName(),
                'email'            => $user->getEntity()->getEmail(),
                'mobile'           => $user->getEntity()->getMobilePhone(),
                'isPhoneConfirmed' => true,
                'isEmailConfirmed' => true,
            ]);
            $session->set($sessionName, $data);

            return new \Http\RedirectResponse(\App::router()->generate('enterprize.create'));
        }
        $session->set($sessionName, $data);

        $flash = $session->get('flash');
        $session->remove('flash');

        $page = new \View\Enterprize\FormPage();
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('form', $this->getForm());
        $page->setParam('errors', !empty($flash['errors']) ? $flash['errors'] : null);
        $page->setParam('authSource', $session->get('authSource', null));
        $page->setParam('viewParams', ['showSideBanner' => false]);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|null
     * @throws \Exception
     */
    public function update(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user()->getEntity();
        $form = new \View\Enterprize\Form();
        $userData = (array)$request->get('user');
        $form->fromArray($userData);

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        if (!$enterprizeToken) {
            $link = \App::router()->generate('enterprize');

            return $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => true,
                    'error'   => null,
                    'data'    => ['link' => $link],
                ])
                : new \Http\RedirectResponse($link);
        }

        if (!isset($userData['subscribe'])) {
            $form->setError('subscribe', 'Необходимо согласие');
        }

        $needAuth = false;
        $response = null;
        $result = null;
        try {
            $authSource = $session->get('authSource', null);
            if ($user) {
                if ('phone' === $authSource && $user->getMobilePhone() !== $form->getMobile()) {
                    throw new \Curl\Exception('Нельзя изменить мобильный телефон');
                } elseif ('email' === $authSource && $user->getEmail() !== $form->getEmail()) {
                    throw new \Curl\Exception('Нельзя изменить email');
                }
            }

            $result = $client->query(
                'coupon/register-in-enter-prize',
                [
                    'client_id' => \App::config()->coreV2['client_id'],
                    'token'     => \App::user()->getToken(),
                ],
                [
                    'name'   => $form->getName(),
                    'mobile' => $form->getMobile(),
                    'email'  => $form->getEmail(),
                    'guid'   => $form->getEnterprizeCoupon(),
                    'agree'  => $form->getAgree(),
                ],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['core.response' => $result], ['coupon', 'register-in-enter-prize']);

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);

            $errorContent = $e->getContent();
            $detail = isset($errorContent['detail']) && is_array($errorContent['detail']) ? $errorContent['detail'] : [];

            if (401 == $e->getCode()) {
                $form->setError('global', $e->getMessage());
                $needAuth = true;

            } elseif (600 == $e->getCode()) {
                $form->setError('global', $e->getMessage());

                foreach ($detail as $fieldName => $errors) {
                    foreach ($errors as $errorType => $errorMess) {
                        switch ($fieldName) {
                            case 'name':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнено имя';
                                } else {
                                    $message = 'Некорректно введено имя';
                                }
                                break;
                            case 'mobile':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнен номер телефона';
                                } elseif ('regexNotMatch' === $errorType) {
                                    $message = 'Некорректно введен номер телефона';
                                }
                                break;
                            case 'email':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не заполнен E-mail';
                                } else {
                                    $message = 'Некорректно введен E-mail';
                                }
                                break;
                            case 'guid':
                                if ('isEmpty' === $errorType) {
                                    $message = 'Не передан идентификатор серии купона';
                                } else {
                                    $message = 'Невалидный идентификатор серии купона';
                                }
                                break;
                            case 'agree':
                                $message = 'Необходимо согласие';
                                break;
                            default:
                                $message = 'Неизвестная ошибка';
                        }

//                        if (\App::config()->debug) {
//                            $message .= ': ' . print_r($errorMess, true);
//                        }

                        $form->setError($fieldName, $message);
                    }
                }
            } elseif (409 == $e->getCode()) {
                $error = 'Уже зарегистрирован в ENTER PRIZE. <a class="bAuthLink" href="'. \App::router()->generate('user.login') .'">Войти</a>';
                if (isset($detail['mobile_in_enter_prize']) && $detail['mobile_in_enter_prize']) {
                    $form->setError('mobile', $error);
                } elseif (isset($detail['email_in_enter_prize']) && $detail['email_in_enter_prize']) {
                    $form->setError('email', $error);
                } else {
                    $form->setError('global', $error);
                }
            } else {
                $form->setError('global', $e->getMessage());
            }
        }

        // Запоминаем данные enterprizeForm
        $data = array_merge($data, [
            'token'            => isset($result['token']) ? $result['token'] : null,
            'name'             => $form->getName(),
            'email'            => $form->getEmail(),
            'mobile'           => $form->getMobile(),
            'isPhoneConfirmed' => isset($result['mobile_confirmed']) ? $result['mobile_confirmed'] : false,
            'isEmailConfirmed' => isset($result['email_confirmed']) ? $result['email_confirmed'] : false,
        ]);
        $session->set($sessionName, $data);

        if ($form->isValid()) {
            $userToken = $data['token'];
            $data = $session->get($sessionName, []);
            if ($data['isPhoneConfirmed'] && $data['isEmailConfirmed']) {
                // пользователь все подтвердил, пробуем создать купон
                $link = \App::router()->generate('enterprize.create');
            } elseif ($data['isPhoneConfirmed']) {
                // просим подтвердит email
                $link = \App::router()->generate('enterprize.confirmEmail.show');
                try {
                    if (!isset($data['email']) || empty($data['email'])) {
                        throw new \Exception('Не получен email');
                    }

                    $confirm = $client->query(
                        'confirm/email',
                        [
                            'client_id' => \App::config()->coreV2['client_id'],
                            'token'     =>  $userToken,
                        ],
                        [
                            'email'    => $data['email'],
                            'template' => 'enter_prize',
                        ],
                        \App::config()->coreV2['hugeTimeout']
                    );
                    \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/email']);

                } catch (\Exception $e) {
                    \App::exception()->remove($e);
                    \App::session()->set('flash', ['error' => $e->getMessage()]);
                }
            } else {
                // просим подтвердить телефон
                $link = \App::router()->generate('enterprize.confirmPhone.show');
                try {
                    if (!isset($data['mobile']) || empty($data['mobile'])) {
                        throw new \Exception('Не получен мобильный телефон');
                    }

                    $confirm = $client->query(
                        'confirm/mobile',
                        [
                            'client_id' => \App::config()->coreV2['client_id'],
                            'token'     => $userToken,
                        ],
                        [
                            'mobile' => $data['mobile'],
                        ],
                        \App::config()->coreV2['hugeTimeout']
                    );
                    \App::logger()->info(['core.response' => $result], ['coupon', 'confirm/mobile']);

                } catch (\Exception $e) {
                    \App::exception()->remove($e);
                    \App::session()->set('flash', ['error' => $e->getMessage()]);
                }
            }

            $response = $request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => true,
                    'error'   => null,
                    'notice'  => ['message' => 'Поздравляем с регистрацией в Enter Prize!', 'type' => 'info'],
                    'data'    => ['link' => $link],
                ])
                : new \Http\RedirectResponse($link);

        } else {
            $formErrors = [];
            foreach ($form->getErrors() as $fieldName => $errorMessage) {
                $formErrors[] = ['code' => 0, 'message' => $errorMessage, 'field' => $fieldName];
            }

            if ($request->isXmlHttpRequest()) {
                $response = new \Http\JsonResponse([
                    'error'    => ['code' => 0, 'message' => 'Не удалось сохранить форму'],
                    'form'     => ['error' => $formErrors],
                    'needAuth' => $needAuth && !\App::user()->getEntity() ? true : false,
                ]);
            } else {
                $errors = [];
                foreach ($form->getErrors() as $fieldName => $errorMessage) {
                    if (!$errorMessage) continue;
                    $errors[$fieldName] = $errorMessage;
                }
                \App::session()->set('flash', ['errors' => $errors]);
            }
        }

        return $response
            ? $response
            : ($request->isXmlHttpRequest()
                ? new \Http\JsonResponse([
                    'success' => true,
                    'error'   => null,
                    'data'    => ['link' => \App::router()->generate('enterprize.form.show', ['enterprizeToken' => $enterprizeToken])],
                ])
                : new \Http\RedirectResponse(\App::router()->generate('enterprize.form.show', ['enterprizeToken' => $enterprizeToken])));
    }

    /**
     * @return \View\Enterprize\Form
     */
    public function getForm(){
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user()->getEntity();
        $session = \App::session();
        $form = new \View\Enterprize\Form();

        $data = $session->get(\App::config()->enterprize['formDataSessionKey'], []);

        // заполняем форму данными с сессии
        $form->fromArray($data);

        // если пользователь авторизован, то заполняем поле по которому произошла авторизация
        if ($user) {
            if (!$form->getName()) {
                $form->setName($user->getFirstName());
            }
            if (!$form->getMobile()) {
                $form->setMobile($user->getMobilePhone());
            }
            if (!$form->getEmail()) {
                $form->setEmail($user->getEmail());
            }

            $authSource = $session->get('authSource', null);
            if ('phone' === $authSource) {
                $form->setMobile($user->getMobilePhone());
            } elseif ('email' === $authSource) {
                $form->setEmail($user->getEmail());
            }
        }

        return $form;
    }
}