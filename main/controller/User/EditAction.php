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

        if ($request->isMethod('post') && !$form->getIsDisabled()) {
            $userData = (array)$request->request->get('user');

            if (!array_key_exists('is_subscribe', $userData)) {
                $userData['is_subscribe'] = false;
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