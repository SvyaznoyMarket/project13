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

        $formData = [
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
            $formData = array_merge($formData, $request->get('user'));
        }

        $flashData = [
            'form'    => 'user',
            'success' => null,
            'errors'  => [],
        ];
        try {
            if (empty($formData['first_name'])) {
                $flashData['errors'][] = ['field' => 'first_name', 'message' => 'Не указано имя'];
            }
            if (!$userEntity->isEnterprizeMember() && empty($formData['email'])) {
                $flashData['errors'][] = ['field' => 'email', 'message' => 'Не указан email'];
            }

            if ($flashData['errors']) {
                throw new \Exception('Форма заполнена неверно');
            }

            $updateQuery = new Query\User\Update();
            $updateQuery->token = $userEntity->getToken();
            $updateQuery->user->firstName = isset($formData['first_name']) ? $formData['first_name'] : '';
            $updateQuery->user->middleName = isset($formData['middle_name']) ? $formData['middle_name'] : '';
            $updateQuery->user->lastName = isset($formData['last_name']) ? $formData['last_name'] : '';
            if (isset($formData['birthday']['year']) && isset($formData['birthday']['month']) && isset($formData['birthday']['day'])) {
                $birthday = sprintf('%04d-%02d-%02d', $formData['birthday']['year'], $formData['birthday']['month'], $formData['birthday']['day']);
                if ('0000-00-00' !== $birthday) {
                    $updateQuery->user->birthday = $birthday;
                }
            }
            $updateQuery->user->sex = isset($formData['sex']) ? $formData['sex'] : null;
            $updateQuery->user->email = isset($formData['email']) ? $formData['email'] : null;
            $updateQuery->user->phone = isset($formData['mobile_phone']) ? $formData['mobile_phone'] : null;
            $updateQuery->user->homePhone = isset($formData['home_phone']) ? $formData['home_phone'] : '';
            $updateQuery->user->occupation = isset($formData['occupation']) ? $formData['occupation'] : '';
            $updateQuery->prepare();

            $this->getCurl()->execute();

            // проверка ошибки
            if ($error = $updateQuery->error) {
                throw $error;
            }

            // если ошибок нет
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