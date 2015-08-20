<?php

namespace Controller\User;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class UpdatePasswordAction extends PrivateAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        $userEntity = \App::user()->getEntity();

        $password = is_string($request->get('password_old')) ? trim($request->get('password_old')) : null;
        $newPassword = is_string($request->get('password_new')) ? trim($request->get('password_new')) : null;
        $repeatedPassword = is_string($request->get('password_repeat')) ? trim($request->get('password_repeat')) : null;

        $flashData = [
            'form'    => 'user.password',
            'success' => null,
            'errors'  => [],
        ];
        try {
            if (empty($password)) {
                $flashData['errors'][] = ['field' => 'password_old', 'message' => 'Не указан старый пароль'];
            }
            if (empty($newPassword)) {
                $flashData['errors'][] = ['field' => 'password_new', 'message' => 'Не указан новый пароль'];
            }
            if (empty($repeatedPassword)) {
                $flashData['errors'][] = ['field' => 'password_repeat', 'message' => 'Не указан новый пароль'];
            }
            if ($repeatedPassword !== $newPassword) {
                $flashData['errors'][] = ['field' => 'password_new', 'message' => 'Пароли не совпадают'];
                $flashData['errors'][] = ['field' => 'password_repeat', 'message' => 'Пароли не совпадают'];
            }

            if ($flashData['errors']) {
                throw new \Exception('Форма заполнена неверно');
            }

            $updateQuery = new Query\User\UpdatePassword();
            $updateQuery->token = $userEntity->getToken();
            $updateQuery->password = $password;
            $updateQuery->newPassword = $newPassword;
            $updateQuery->prepare();

            $this->getCurl()->execute();

            // проверка ошибки
            if ($error = $updateQuery->error) {
                throw $error;
            }

            // если ошибок нет, значит пароль изменен успешно
            $flashData['success'] = true;
        } catch (\Exception $error) {
            $flashData['success'] = false;

            switch ($error->getCode()) {
                case 613:
                    $flashData['errors'][] = ['field' => 'password_old', 'message' => 'Пароль не подходит'];
                    break;
                default:
                    if (!$flashData['errors']) {
                        $flashData['errors'][] = ['field' => null, 'message' => 'Не удалось изменить пароль'];
                    }
                    break;
            }
        }

        //die(var_dump($flashData));
        \App::session()->flash($flashData);

        return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
    }
}