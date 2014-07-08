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

            if (!array_key_exists('is_subscribe', $userData)) {
                $userData['is_subscribe'] = false;
            }

            if (array_key_exists('bonus_card', $userData) && (bool)$userData['bonus_card'] && is_array($userData['bonus_card'])) {
                $bonusCards = [];
                foreach ($userData['bonus_card'] as $id => $number) {
                    $bonusCards[] = [
                        'bonus_card_id' => $id,
                        'number' => $number,
                    ];
                }

                $userData['bonus_card'] = $bonusCards;
            }

            $form->fromArray($userData);

            try {
                $tmp = rtrim( $form->getEmail() ) . rtrim($form->getMobilePhone());
                if ( empty($tmp) ) {
                    throw new \Exception("E-mail и телефон не могут быть одновременно пустыми. Укажите ваш мобильный телефон либо e-mail.");
                }

                // пользователь является EnterPrize Member
                if (
                    ($form->getIsDisabled() && $userEntity) &&
                    ($userEntity->getEmail() !== $form->getEmail() || $userEntity->getMobilePhone() !== $form->getMobilePhone())
                ) {
                    throw new \Exception("E-mail и телефон не могут быть отредактированы.");
                }

                // Смена пароля
                $oldPassword = trim((string)$request->get('password_old'));
                $newPassword = trim((string)$request->get('password_new'));

                if ( ($oldPassword && !$newPassword) || (!$oldPassword && $newPassword)) {
                    throw new \Exception("Не заполнено одно из полей смены пароля");
                }

                if ($newPassword && $oldPassword == $newPassword) {
                    throw new \Exception("Старый пароль совпадает с новым паролем");
                }

                if ($newPassword) {
                    $response_change_password = $client->query(
                        'user/change-password',
                        [
                            'token'         => \App::user()->getToken(),
                            'password'      => $oldPassword,
                            'new_password'  => $newPassword
                        ]
                    );
                }

                $response_change_info = $client->query(
                    'user/update',
                    ['token' => \App::user()->getToken()],
                    [
                        'first_name'    => $form->getFirstName(),
                        'middle_name'   => $form->getMiddleName(),
                        'last_name'     => $form->getLastName(),
                        'sex'           => $form->getSex(),
                        'email'         => $form->getEmail(),
                        'mobile'        => $form->getMobilePhone(),
                        'phone'         => $form->getHomePhone(),
                        'birthday'      => $form->getBirthday() ? $form->getBirthday()->format('Y-m-d') : null,
                        'occupation'    => $form->getOccupation(),
                        'bonus_card'    => $form->getBonusCardNumbers(),
                        'is_subscribe'  => $form->getIsSubscribed(),
                    ],
                    \App::config()->coreV2['hugeTimeout']
                );

                if (!isset($response_change_info['confirmed']) || !$response_change_info['confirmed']) {
                    throw new \Exception('Не удалось сменить данные пользователя.');
                }

                if (isset($response_change_password) && (!isset($response_change_password['confirmed']) || !$response_change_password['confirmed'])) {
                    throw new \Exception('Не удалось сменить пароль пользователя.');
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

        $bonusCards = [];
        try {
            $bonusCards = \RepositoryManager::bonusCard()->getCollection();
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        $page = new \View\User\EditPage();
        $page->setParam('form', $form);
        $page->setParam('message', $message);
        $page->setParam('redirect', $redirect);
        $page->setParam('bonusCards', $bonusCards);

        return new \Http\Response($page->show());
    }
}