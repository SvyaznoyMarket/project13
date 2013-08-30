<?php

namespace Controller\User;

class SubscribeAction {

    const EMPTY_PHONE_ERROR = 'Не указан мобильный телефон';
    const INVALID_PHONE_ERROR = 'Номер мобильного телефона должен содержать 11 цифр';
    const SAVE_FAILED_ERROR = 'Не удалось сохранить данные';

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $isSubscribe = (bool)$request->request->get('subscribe', false);
        $isSubscribeViaSms = (bool)$request->request->get('subscribe_sms', false);

        // мобильный телефон
        $mobilePhone = $request->request->get('mobile_phone', false);
        $mobilePhone = trim((string)$mobilePhone);
        $mobilePhone = preg_replace('/^\+7/', '8', $mobilePhone);
        $mobilePhone = preg_replace('/[^\d]/', '', $mobilePhone);
        if (10 == strlen($mobilePhone)) {
            $mobilePhone = '8' . $mobilePhone;
        }

        $error = null;
        $smsTmpCheck = null;
        
        if ($request->isMethod('post')) {

            $userData = [
                'is_subscribe'      => $isSubscribe,
                'is_sms_subscribe'  => $isSubscribeViaSms,
            ];

            if($isSubscribeViaSms) {
                if(empty($mobilePhone)) {
                    $error = self::EMPTY_PHONE_ERROR;
                } elseif (11 != strlen($mobilePhone)) {
                    $error = self::INVALID_PHONE_ERROR;
                } else {
                    $userData['mobile_phone'] = $mobilePhone;
                }
                $smsTmpCheck = true;
            }

            try {
                if ($error) {
                    throw new \Exception($error);
                }

                $result = \App::coreClientV2()->query('user/update', array('token' => \App::user()->getToken()), $userData);

                if (!isset($result['confirmed']) || !$result['confirmed']) {
                    throw new \Exception(self::SAVE_FAILED_ERROR);
                }

                return new \Http\RedirectResponse(\App::router()->generate('user'));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                switch ($e->getMessage()) {
                    case self::EMPTY_PHONE_ERROR:
                    case self::INVALID_PHONE_ERROR:
                    case self::SAVE_FAILED_ERROR:
                        $error = $e->getMessage();
                        break;
                    default:
                        $error = self::SAVE_FAILED_ERROR;
                        break;
                }
            }

        }

        $page = new \View\User\IndexPage();
        $page->setParam('error', $error);
        $page->setParam('smsTmpCheck', $smsTmpCheck);

        return new \Http\Response($page->show());
    }
}