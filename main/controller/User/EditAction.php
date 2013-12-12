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

                if (!$form->getIsSubscribed()) {
                    throw new \Exception('Не отмечено поле "Согласен получать рекламную рассылку"');
                }

                $response = $client->query(
                    'user/update',
                    ['token' => \App::user()->getToken()],
                    [
                        'first_name'   => $form->getFirstName(),
                        'middle_name'  => $form->getMiddleName(),
                        'last_name'    => $form->getLastName(),
                        'sex'          => $form->getSex(),
                        'email'        => $form->getEmail(),
                        'mobile'       => $form->getMobilePhone(),
                        'phone'        => $form->getHomePhone(),
                        'birthday'     => $form->getBirthday() ? $form->getBirthday()->format('Y-m-d') : null,
                        'occupation'   => $form->getOccupation(),
                        'is_subscribe' => $form->getIsSubscribed(),
                    ],
                    \App::config()->coreV2['hugeTimeout']
                );

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не получен ответ от сервера.');
                }

                if ($form->getEnterprizeCoupon()) {
                    try {
                        if (!$form->getLastName()) {
                            throw new \Exception('Не заполнена фамилия');
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
                                'guid'                      => $form->getEnterprizeCoupon(),
                                'agree'                     => $form->getCouponAgree(),
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

                        if ($result instanceof \Curl\Exception) {
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

                        $session->set('flash', 'Данные сохранены. Купон вам отправлен по СМС и е-майл.');

                        return new \Http\RedirectResponse($redirect);
                    } catch (\Curl\Exception $e) {
                        \App::exception()->remove($e);
                        $errorContent = $e->getContent();
                        $detail = isset($errorContent['detail']) ? $errorContent['detail'] : [];

                        foreach ($detail as $fieldName => $errors) {
                            foreach ($errors as $errorType => $errorMess) {
                                if ('name' == $fieldName) $fieldName = 'first_name';
                                if ('phone' == $fieldName) $fieldName = 'mobile_phone';

                                $form->setError($fieldName, $errorMess);
                            }
                        }

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