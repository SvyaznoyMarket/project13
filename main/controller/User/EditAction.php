<?php

namespace Controller\User;

class EditAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();
        $client = \App::coreClientV2();
        $userEntity = \App::user()->getEntity();

        $form = new \View\User\EditForm();
        $form->fromEntity($userEntity);

        $message = $session->get('flash');
        $session->remove('flash');

        $redirect = $request->get('redirect_to')
            ? $request->get('redirect_to')
            : \App::router()->generate('user.edit');

        if(!preg_match('/^(\/|http).*/i', $redirect)) {
            $redirect = 'http://' . $redirect;
        }

        if ($request->isMethod('post')) {
            $userData = (array)$request->request->get('user');
            $form->fromArray($userData);

            try {
                $tmp = rtrim( $form->getEmail() ) . rtrim($form->getMobilePhone());
                if ( empty($tmp) ) {
                    throw new \Exception("E-mail и телефон не могут быть одновременно пустыми. Укажите ваш мобильный телефон либо e-mail.");
                }

                $response = $client->query(
                    'user/update',
                    ['token' => \App::user()->getToken()],
                    [
                        'first_name'  => $form->getFirstName(),
                        'middle_name' => $form->getMiddleName(),
                        'last_name'   => $form->getLastName(),
                        'sex'         => $form->getSex(),
                        'email'       => $form->getEmail(),
                        'mobile'      => $form->getMobilePhone(),
                        'phone'       => $form->getHomePhone(),
                        'skype'       => $form->getSkype(),
                        'birthday'    => $form->getBirthday() ? $form->getBirthday()->format('Y-m-d') : null,
                        'occupation'  => $form->getOccupation(),
                    ],
                    \App::config()->coreV2['hugeTimeout']
                );

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не получен ответ от сервера.');
                }

                if ($couponType = $request->get('enterprize_coupon')) {
                    try {
                        // проверяем заполнил ли пользователь все поля формы (кроме "Род деятельности")
                        if (!$form->getFirstName()) {
                            throw new \Exception('Не передано имя.');
                        }
                        if (!$form->getMiddleName()) {
                            throw new \Exception('Не передано отчество.');
                        }
                        if (!$form->getLastName()) {
                            throw new \Exception('Не передана фамилия.');
                        }
                        if (!$form->getSex()) {
                            throw new \Exception('Не передан пол.');
                        }
                        if (!$form->getEmail()) {
                            throw new \Exception('Не передан email.');
                        }
                        if (!$form->getMobilePhone()) {
                            throw new \Exception('Не передан мобильный телефон.');
                        }
                        if (!$form->getHomePhone()) {
                            throw new \Exception('Не передан домашний телефон.');
                        }
                        if (!$form->getSkype()) {
                            throw new \Exception('Не передан skype.');
                        }
                        if (!$form->getBirthday()) {
                            throw new \Exception('Не передана дата рождения.');
                        }

                        // создание enterprize-купона
                        $result = [];
                        $client->addQuery(
                            'coupon/enter-prize',
                            [
                                'client_id' => \App::config()->coreV2['client_id'],
                                'token'     => \App::user()->getToken(),
                            ],
                            [
                                'name'                      => $form->getFirstName(),
                                'phone'                     => $form->getMobilePhone(),
                                'email'                     => $form->getEmail(),
                                'svyaznoy_club_card_number' => null,
                                'guid'                      => $couponType,
                                'agree'                     => true,
                            ],
                            function ($data) use (&$result) {
                                $result = $data;
                            },
                            function(\Exception $e) use (&$result) {
                                \App::exception()->remove($e);
                                $result = $e;
                            }
                        );
                        $client->execute();

                        if ($result instanceof \Exception) {
                            throw $result;
                        }

                        // помечаем пользователя как получившего enterprize-купон
                        $response = $client->query(
                            'user/update',
                            ['token' => \App::user()->getToken()],
                            [
                                'coupon_enter_prize' => 1
                            ],
                            \App::config()->coreV2['hugeTimeout']
                        );

                        if (!isset($response['confirmed']) || !$response['confirmed']) {
                            throw new \Exception('Не получен ответ от сервера.');
                        }
                    } catch (\Exception $e) {
                        \App::exception()->remove($e);
                        throw $e;
                    }
                }

                $session->set('flash', 'Данные сохранены');

                return new \Http\RedirectResponse($redirect);
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e, ['user']);

                $errorMess = $e->getMessage();
                $form->setError('global', 'Не удалось сохранить форму. ' . $errorMess);
            }
        }

        $page = new \View\User\EditPage();
        $page->setParam('form', $form);
        $page->setParam('message', $message);
        $page->setParam('redirect', $redirect);

        return new \Http\Response($page->show());
    }
}