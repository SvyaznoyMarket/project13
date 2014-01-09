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

        if ($request->get('enterprize_coupon')) {
            $form->setEnterprizeCoupon($request->get('enterprize_coupon'));
        }

        $message = $session->get('flash');
        $session->remove('flash');

        $redirect = $request->get('redirect_to')
            ? $request->get('redirect_to')
            : \App::router()->generate('user.edit', ['enterprize_coupon' => $form->getEnterprizeCoupon()]);

        if(!preg_match('/^(\/|http).*/i', $redirect)) {
            $redirect = 'http://' . $redirect;
        }

        if ($request->isMethod('post')) {
            $userData = (array)$request->request->get('user');

            if (!array_key_exists('is_subscribe', $userData)) {
                $userData['is_subscribe'] = false;
            }
            if (!array_key_exists('coupon_agree', $userData)) {
                $userData['coupon_agree'] = false;
            }

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
                        'first_name'                => $form->getFirstName(),
                        'middle_name'               => $form->getMiddleName(),
                        'last_name'                 => $form->getLastName(),
                        'sex'                       => $form->getSex(),
                        'email'                     => $form->getEmail(),
                        'mobile'                    => $form->getMobilePhone(),
                        'phone'                     => $form->getHomePhone(),
                        'birthday'                  => $form->getBirthday() ? $form->getBirthday()->format('Y-m-d') : null,
                        'occupation'                => $form->getOccupation(),
                        'svyaznoy_club_card_number' => $form->getSclubCardnumber(),
                        'is_subscribe'              => $form->getIsSubscribed(),
                    ],
                    \App::config()->coreV2['hugeTimeout']
                );

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не получен ответ от сервера.');
                }

                if ($form->getEnterprizeCoupon() && !$userEntity->getEnterprizeCoupon()) {
                    try {
                        if (!$form->getLastName()) {
                            $form->setError('last_name', 'Не указана фамилия');
                        }

                        if (!$form->getIsSubscribed()) {
                            $form->setError('is_subscribe', 'Не отмечено поле "Согласен получать рекламную рассылку"');
                        }

                        // создание enterprize-купона
                        $result = $client->query(
                            'coupon/enter-prize',
                            [
                                'client_id' => \App::config()->coreV2['client_id'],
                                'token'     => \App::user()->getToken(),
                            ],
                            [
                                'name'                      => $form->getFirstName(),
                                'phone'                     => $form->getMobilePhone(),
                                'email'                     => $form->getEmail(),
                                'svyaznoy_club_card_number' => $form->getSclubCardnumber(),
                                'guid'                      => $form->getEnterprizeCoupon(),
                                'agree'                     => $form->getCouponAgree(),
                            ],
                            \App::config()->coreV2['hugeTimeout']
                        );

                        if ($form->getError('last_name')) {
                            throw new \Curl\Exception($form->getError('last_name'));
                        }

                        if ($form->getError('is_subscribe')) {
                            throw new \Curl\Exception($form->getError('is_subscribe'));
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
                            throw new \Exception('Не получен ответ от сервера');
                        }
                    } catch (\Curl\Exception $e) {
                        \App::exception()->remove($e);

                        if (422 == $e->getCode()) {
                            $errorContent = $e->getContent();
                            $detail = isset($errorContent['detail']) && is_array($errorContent['detail']) ? $errorContent['detail'] : [];

                            foreach ($detail as $fieldName => $errors) {
                                foreach ($errors as $errorType => $errorMess) {
                                    if ('name' == $fieldName) $fieldName = 'first_name';
                                    if ('phone' == $fieldName) $fieldName = 'mobile_phone';

                                    $message = 'Неизвестная ошибка';

                                    switch ($fieldName) {
                                        case 'first_name':
                                            if ('isEmpty' === $errorType) $message = 'Не заполнено имя';
                                            if ('regexNotMatch' === $errorType) $message = 'Некорректно введено имя';
                                            break;
                                        case 'mobile_phone':
                                            if ('isEmpty' === $errorType) $message = 'Не заполнен номер телефона';
                                            if ('regexNotMatch' === $errorType) $message = 'Некорректно введен номер телефона';
                                            break;
                                        case 'email':
                                            if ('isEmpty' === $errorType) $message = 'Не заполнен E-mail';
                                            if ('regexNotMatch' === $errorType) $message = 'Некорректно введен номер телефона';
                                            break;
                                        case 'svyaznoy_club_card_number':
                                            if ('isEmpty' === $errorType) $message = 'Не заполнен номер карты Связной-Клуб';
                                            if ('regexNotMatch' === $errorType) $message = 'Некорректно введен номер карты Связной-Клуб';
                                            if ('checksumInvalid' === $errorType) $message = 'Некорректно введен номер карты Связной-Клуб';
                                            break;
                                        case 'guid':
                                            if ('isEmpty' === $errorType) $message = 'Не передан купон';
                                            if ('regexNotMatch' === $errorType) $message = 'Невалидный идентификатор серии купона';
                                            break;
                                        case 'agree':
                                            if ('isEmpty' === $errorType) $message = 'Не отмечено поле "Ознакомлен с правилами ENTER PRIZE"';
                                            break;
                                    }

                                    if (\App::config()->debug) {
                                        $message .= ': ' . print_r($errorMess, true);
                                    }

                                    $form->setError($fieldName, $message);
                                }
                            }
                        }

                        // Если есть ошибка в поле guid ('Идентификатор серии купона'), то подставляем данную ошибку в global-error
                        $errorMess = $form->getError('guid') ? $form->getError('guid') : $e->getMessage();
                        $form->setError('global', 'Не удалось сохранить форму. ' . $errorMess);

                        if (!$request->isXmlHttpRequest()) {
                            throw $e;
                        }
                    }

                    // xhr
                    if ($request->isXmlHttpRequest()) {
                        $formErrors = [];
                        foreach ($form->getErrors() as $fieldName => $errorMessage) {
                            $formErrors[] = ['code' => 0, 'message' => $errorMessage, 'field' => $fieldName];
                        }

                        $responseData = $form->isValid()
                            ? ['error' => null, 'notice' => ['message' => 'Поздравляем с регистрацией в Enter Prize! Фишка отправлена на мобильный телефон и e-mail.', 'type' => 'info']]
                            : ['error' => ['code' => 0, 'message' => 'Не удалось сохранить форму'], 'form' => ['error' => $formErrors]];

                        return new \Http\JsonResponse($responseData);
                    }

                    $session->set('flash', 'Поздравляем с регистрацией в Enter Prize! Фишка отправлена на мобильный телефон и e-mail.');

                    return new \Http\RedirectResponse($redirect);
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

        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
        $enterpizeCoupon = null;
        if ($form->getEnterprizeCoupon()) {
            \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupon, $form) {
                foreach ((array)$data as $item) {
                    if ($form->getEnterprizeCoupon() == $item['token']) {
                        $enterpizeCoupon = new \Model\EnterprizeCoupon\Entity($item);
                    }
                }
            });
            \App::dataStoreClient()->execute();
        }

        $page = $form->getEnterprizeCoupon() ? new \View\User\EditEnterprizePage() : new \View\User\EditPage();
        $page->setParam('form', $form);
        $page->setParam('message', $message);
        $page->setParam('redirect', $redirect);
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);

        return new \Http\Response($page->show());
    }
}