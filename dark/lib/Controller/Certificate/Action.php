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
        $code = trim($request->get('code'));
        if (!$code) {
            return new \Http\JsonResponse(array('success' => false, 'error' => 'Не указан код сертификата'));
        }

        $error = 'Неверный сертификат';
        try {
            $response = \App::coreClientV2()->query('certificate/check', array('code' => $code));
            if (is_array($response) && array_key_exists('error', $response)) {
                $e = new \Core\Exception($response['error']['message'], $response['error']['code']);

                throw $e;
            }

            $statusCode = (int)$response['status_code'];
            if (0 == $statusCode) {
                return new \Http\JsonResponse(array('success' => true));
            } else {
                if (array_key_exists($statusCode, $this->errors)) {
                    $error = $this->errors[$statusCode];
                }
            }
        } catch (\Core\Exception $e) {
            \App::logger()->warn('Error when checking certificate ' . $e);
            if (-1 == $e->getCode()) {
                $error = 'Сертификат не найден';
            }
        }

        return new \Http\JsonResponse(array('success' => false, $error => $error));
    }
}