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
            throw new \Exception\NotFoundException();
        }

        $user = \App::user()->getEntity();

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];
        if (!$session->has($sessionName)) {
            $session->set($sessionName, [
                'isPhoneConfirmed' => false,
                'isEmailConfirmed' => false,
            ]);
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

        $form = $this->getForm();
        $form->setEnterprizeCoupon($enterprizeToken);

        $page = new \View\Enterprize\FormPage();
        $page->setParam('user', $user);
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     */
    public function update(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $form = new \View\Enterprize\Form();
        $userData = (array)$request->get('user');
        $form->fromArray($userData);

        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        if (!isset($userData['subscribe'])) {
            $form->setError('subscribe', 'Необходимо согласие');
        }

        $needAuth = false;
        $response = null;
        $result = null;
        try {
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

            $form->setError('global', $e->getMessage());

            if (401 == $e->getCode()) {
                $needAuth = true;

            } elseif (600 == $e->getCode()) {
                $errorContent = $e->getContent();
                $detail = isset($errorContent['detail']) && is_array($errorContent['detail']) ? $errorContent['detail'] : [];

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
            }
        }

        if ($form->isValid()) {
            // Запоминаем данные enterprizeForm
            $session->set($sessionName, [
                'name'             => $form->getName(),
                'email'            => $form->getEmail(),
                'mobile'           => $form->getMobile(),
                'isPhoneConfirmed' => isset($result['mobile_confirmed']) ? $result['mobile_confirmed'] : false,
                'isEmailConfirmed' => isset($result['email_confirmed']) ? $result['email_confirmed'] : false,
            ]);

            $userToken = !empty($result['token']) ? $result['token'] : null;
            $data = $session->get($sessionName, []);
            if ($data['isPhoneConfirmed'] && $data['isEmailConfirmed']) {
                // пользователь все подтвердил, пробуем создать купон
                $link = \App::router()->generate('enterprize.create');
            } elseif ($data['isPhoneConfirmed']) {
                // просим подтвердит email
                $link = \App::router()->generate('enterprize.confirmEmail.show', ['enterprizeToken' => $form->getEnterprizeCoupon()]);
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
                $link = \App::router()->generate('enterprize.confirmPhone.show', ['enterprizeToken' => $form->getEnterprizeCoupon()]);
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

                } catch (\Curl\Exception $e) {
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

            // авторизовываем пользователя
            if ($userToken && !\App::user()->getEntity()) {
                $user = \RepositoryManager::user()->getEntityByToken($result['token']);
                if ($user) {
                    $user->setToken($result['token']);
                    \App::user()->signIn($user, $response);
                } else {
                    \App::logger()->error(sprintf('Не удалось получить пользователя по токену %s', $result['token']));
                }
            }

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
            }
        }

        return $response ? $response
            : new \Http\RedirectResponse(\App::router()->generate('enterprize.form.show', ['enterprizeToken' => $form->getEnterprizeCoupon()]));
    }

    /**
     * @return \View\Enterprize\Form
     * @throws \Exception\NotFoundException
     */
    public function getForm(){
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user()->getEntity();
        $form = new \View\Enterprize\Form();

        // пользователь авторизован, заполняем форму данными пользователя
        if ($user) {
            $form->fromEntity($user);

            // иначе, заполняем форму данными с сессии
        } else {
            $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []);
            $form->fromArray($data);
        }

        return $form;
    }
}