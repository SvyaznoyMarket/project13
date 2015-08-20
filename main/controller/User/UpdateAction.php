<?php

namespace Controller\User;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class UpdateAction extends PrivateAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        $userEntity = \App::user()->getEntity();

        $userData = [
            'first_name'   => null,
            'middle_name'  => null,
            'last_name'    => null,
            'birthday'     => null,
            'sex'          => null,
            'email'        => null,
            'mobile_phone' => null,
            'home_phone'   => null,
            'occupation'   => null,
        ];
        if (is_array($request->get('user'))) {
            $userData = array_merge($userData, $request->get('user'));
        }

        $flashData = [
            'form'    => 'user',
            'success' => null,
            'errors'  => [],
        ];
        try {
            if (empty($userData['first_name'])) {
                $flashData['errors'][] = ['field' => 'user[first_name]', 'message' => 'Не указано имя'];
            }
            if (!$userEntity->isEnterprizeMember() && empty($userData['email'])) {
                $flashData['errors'][] = ['field' => 'user[email]', 'message' => 'Не указан email'];
            }

            if ($flashData['errors']) {
                throw new \Exception('Форма заполнена неверно');
            }

            $updateQuery = new Query\User\Update();
            $updateQuery->token = $userEntity->getToken();
            $updateQuery->user->firstName = isset($userData['first_name']) ? $userData['first_name'] : null;
            $updateQuery->user->middleName = isset($userData['middle_name']) ? $userData['middle_name'] : null;
            $updateQuery->user->lastName = isset($userData['last_name']) ? $userData['last_name'] : null;
            if (isset($userData['birthday']['year']) && isset($userData['birthday']['month']) && isset($userData['birthday']['day'])) {
                $updateQuery->user->birthday = sprintf('%04d-%02d-%02d', $userData['birthday']['year'], $userData['birthday']['month'], $userData['birthday']['day']);
            }
            $updateQuery->user->sex = isset($userData['sex']) ? $userData['sex'] : null;
            $updateQuery->user->email = isset($userData['email']) ? $userData['email'] : null;
            $updateQuery->user->phone = isset($userData['mobile_phone']) ? $userData['mobile_phone'] : null;
            $updateQuery->user->homePhone = isset($userData['home_phone']) ? $userData['home_phone'] : null;
            $updateQuery->user->occupation = isset($userData['occupation']) ? $userData['occupation'] : null;
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
                default:
                    if (!$flashData['errors']) {
                        $flashData['errors'][] = ['field' => null, 'message' => 'Не удалось сохранить данные'];
                    }
                    break;
            }
        }

        //die(var_dump($flashData));
        \App::session()->flash($flashData);

        return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
    }
}