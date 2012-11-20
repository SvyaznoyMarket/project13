<?php

namespace Controller\Certificate;

class Action {
    private $errors = array(
        1 => 'Сертификат заблокирован',
        2 => 'Сертификат не активирован',
        3 => 'Сертификат погашен',
        4 => 'Истек срок действия сертификата',

    );

    public function check(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $code = trim($request->get('code'));
        $pin = trim($request->get('pin'));
        if (!$code || !$pin) {
            return new \Http\JsonResponse(array('success' => false, 'error' => 'Не указан код или пин сертификата'));
        }

        $error = 'Неверный сертификат';
        try {
            $result = \App::coreClientV2()->query('certificate/check', array('code' => $code, 'pin' => $pin));
            if (is_array($result) && array_key_exists('error', $result)) {
                $e = new \Core\Exception($result['error']['message'], $result['error']['code']);

                throw $e;
            }

            $statusCode = (int)$result['status_code'];
            if (0 == $statusCode) {
                return new \Http\JsonResponse(array('success' => true));
            } else {
                if (array_key_exists($statusCode, $this->errors)) {
                    $error = $this->errors[$statusCode];
                }
            }
        } catch (\Exception $e) {
            \App::logger()->warn('Error when checking certificate ' . $e);
            if (-1 == $e->getCode()) {
                $error = 'Сертификат не найден';
            }
        }

        return new \Http\JsonResponse(array('success' => false, 'error' => $error));
    }
}