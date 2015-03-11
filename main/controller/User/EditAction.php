<?php

namespace Controller\User;

class EditAction {

    private $client;
    private $user;
    private $session;

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
        $this->client = \App::coreClientV2();
        $this->user = \App::user()->getEntity();
        $this->session = \App::session();
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if ($request->isMethod('post')) {
            try {
                $this->setData($request);
                $this->session->flash(['type' => 'success', 'message' => 'Ваш профиль обновлён']);
            } catch (\Curl\Exception $e) {
                \App::logger()->error($e, ['error', 'curl']);
                \App::exception()->remove($e);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить данные']);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['error']);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить данные']);
            }
            return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData($request)
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\EditPage();
        $page->setParam('form', $data['form']);
        $page->setParam('flash', $this->session->flash());
        $page->setParam('redirect', $data['redirect']);
        $page->setParam('bonusCards', $data['bonusCards']);

        return new \Http\Response($page->show());
    }

    private function getData(\Http\Request $request) {

        $form = new \View\User\EditForm();
        $form->fromEntity($this->user);

        $message = $this->session->get('flash');
        $this->session->remove('flash');

        $redirect = $request->get('redirect_to')
            ? $request->get('redirect_to')
            : \App::router()->generate('user.edit');

        if(!preg_match('/^(\/|http).*/i', $redirect)) {
            $redirect = 'http://' . $redirect;
        }

        $bonusCards = [];

        try {
            $bonusCards = \RepositoryManager::bonusCard()->getCollection();
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        return ['form' => $form, 'message' => $message, 'redirect' => $redirect, 'bonusCards' => $bonusCards];
    }

    private function setData(\Http\Request $request) {

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

        $form = new \View\User\EditForm();
        $form->fromArray($userData);

        $tmp = rtrim( $form->getEmail() ) . rtrim($form->getMobilePhone());
        if ( empty($tmp) ) {
            throw new \Exception("E-mail и телефон не могут быть одновременно пустыми. Укажите ваш мобильный телефон либо e-mail.");
        }

        // пользователь является EnterPrize Member
        if (
            ($form->getIsDisabled() && $this->user) &&
            ($this->user->getEmail() !== $form->getEmail() || $this->user->getMobilePhone() !== $form->getMobilePhone())
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
            $response_change_password = $this->client->query(
                'user/change-password',
                [
                    'token'         => \App::user()->getToken(),
                    'password'      => $oldPassword,
                    'new_password'  => $newPassword
                ]
            );
        }

        $response_change_info = $this->client->query(
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
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function editSclubNumber(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        try {
            $number = trim($request->get('number'));
            if (!$number) {
                throw new \Exception('Не передан номер Связного');
            }

            $sclubId = \Model\Order\BonusCard\Entity::SVYAZNOY_ID;
            $userBonusCards = $this->user->getBonusCard() ?: [];

            // подставляем новый номер Связного
            $isEdit = false;
            foreach ($userBonusCards as $key => $card) {
                if (isset($card['bonus_card_id']) && $card['bonus_card_id'] == $sclubId) {
                    $userBonusCards[$key]['number'] = $number;
                    $isEdit = true;
                }
            }

            if (!$isEdit) {
                $userBonusCards[] = ['bonus_card_id' => $sclubId, 'number' => $number];
                $isEdit = true;
            }

            // формируем массив номеров бонусных карт
            $bonusCardNumbers = [];
            if (is_array($userBonusCards) && !empty($userBonusCards)) {
                $bonusCardNumbers = array_filter(array_map(function($card){
                    return isset($card['number']) && !empty($card['number']) ? $card['number'] : null;
                }, $userBonusCards));
            }

            $result = $this->client->query('user/update', ['token' => \App::user()->getToken()],
                ['bonus_card' => $bonusCardNumbers], \App::config()->coreV2['hugeTimeout']);

            if (!isset($result['confirmed']) || !$result['confirmed']) {
                throw new \Exception('Не получен ответ от сервера.');
            }

            $responseData = ['success' => true];

        } catch(\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);

            $responseData = [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}