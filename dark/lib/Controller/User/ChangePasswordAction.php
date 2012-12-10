<?php

namespace Controller\User;

class ChangePasswordAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $error = null;

        $oldPassword = trim((string)$request->get('password_old'));
        $newPassword = trim((string)$request->get('password_new'));

        if ($request->isMethod('post')) {
            try {
                if (!$oldPassword || !$newPassword) {
                    throw new \Exception('Old or new password not provided', 1000);
                }
                $response = \App::coreClientV2()->query('user/change-password', array(
                    'token'        => \App::user()->getToken(),
                    'password'     => $oldPassword,
                    'new_password' => $newPassword,
                ));
                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                return new \Http\RedirectResponse(\App::router()->generate('user.changePassword'));
            } catch (\Exception $e) {
                \App::$exception = null;
                \App::logger()->error($e);
                switch ($e->getCode()) {
                    case 1000:
                        $error = 'Не заполнено одно из обязательных полей';
                        break;
                    case 613:
                        $error = 'Пароль не подходит';
                        break;
                    default:
                        $error = 'Не удалось сохранить форму';
                        break;
                }
            }
        }

        $page = new \View\User\ChangePasswordPage();
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }
}