<?php

namespace Controller\User;

class SubscribeAction extends PrivateAction {

    const EMPTY_PHONE_ERROR = 'Не указан мобильный телефон';
    const INVALID_PHONE_ERROR = 'Номер мобильного телефона должен содержать 11 цифр';
    const OCCUPIED_PHONE_ERROR = 'Такой номер уже занят';
    const EMPTY_EMAIL_ERROR = 'Не указан email';
    const INVALID_EMAIL_ERROR = 'Указан некорректный email';
    const OCCUPIED_EMAIL_ERROR = 'Такой email уже занят';
    const SAVE_FAILED_ERROR = 'Не удалось сохранить данные';

    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $helper = new \View\Helper();

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

        // email
        $email = $request->request->get('email', false);

        $error = null;
        $smsTmpCheck = null;
        $emailTmpCheck = null;
        
        if ($request->isMethod('post')) {

            $userData = [
                'is_subscribe'      => $isSubscribe,
                'is_sms_subscribe'  => $isSubscribeViaSms,
            ];

            if($isSubscribeViaSms) {
                if(empty($mobilePhone)) {
                    $error = self::EMPTY_PHONE_ERROR;
                    $smsTmpCheck = true;
                } elseif (11 != strlen($mobilePhone)) {
                    $error = self::INVALID_PHONE_ERROR;
                    $smsTmpCheck = true;
                } else {
                    $userData['mobile'] = $mobilePhone;
                }
            }


            $emailValidator = new \Validator\Email();
            if($isSubscribe) {
                if(empty($email)) {
                    $error .= empty($error) ? self::EMPTY_EMAIL_ERROR : ', ' . $helper->mbyte_ucfirst(self::EMPTY_EMAIL_ERROR);
                    $emailTmpCheck = true;
                } elseif (!$emailValidator->isValid($email)) {
                    $error .= empty($error) ? self::INVALID_EMAIL_ERROR : ', ' . $helper->mbyte_ucfirst(self::INVALID_EMAIL_ERROR);
                    $emailTmpCheck = true;
                } else {
                    $userData['email'] = $email;
                }
            }

            try {
                if ($error) {
                    throw new \Exception($error);
                }

                $result = \App::coreClientV2()->query('user/update', array('token' => \App::user()->getToken()), $userData);

                if (!isset($result['confirmed']) || !$result['confirmed']) {
                    throw new \Exception(self::SAVE_FAILED_ERROR);
                }

                $response = new \Http\RedirectResponse(\App::router()->generate(\App::config()->user['defaultRoute']));

                // передаем email пользователя для RetailRocket
                if (!empty($email)) {
                    \App::retailrocket()->setUserEmail($response, $email);
                }

                return $response;

            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                switch ($e->getMessage()) {
                    case self::EMPTY_PHONE_ERROR:
                    case self::INVALID_PHONE_ERROR:
                    case self::OCCUPIED_PHONE_ERROR:
                        $smsTmpCheck = true;
                        $error = $e->getMessage();
                        break;
                    case self::EMPTY_EMAIL_ERROR:
                    case self::INVALID_EMAIL_ERROR:
                    case self::OCCUPIED_EMAIL_ERROR:
                        $emailTmpCheck = true;
                        $error = $e->getMessage();
                        break;
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
        $page->setParam('emailTmpCheck', $emailTmpCheck);

        return new \Http\Response($page->show());
    }
}