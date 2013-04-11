<?php

namespace Controller\Certificate;

class Action {
    private $errors = [
        1 => 'Сертификат заблокирован',
        2 => 'Сертификат не активирован',
        3 => 'Сертификат погашен',
        4 => 'Истек срок действия сертификата',

    ];

    /**
     * @param \Http\Request $request
     * @throws \Exception
     * @return \Http\JsonResponse
     */
    public function check(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $code = trim($request->get('code'));
        $pin = trim($request->get('pin'));
        if (!$code || !$pin) {
            return new \Http\JsonResponse(['success' => false, 'error' => 'Не указан код или пин сертификата']);
        }

        $error = 'Неверный сертификат';
        try {
            $exception = null;
            $result = null;
            \App::coreClientV2()->addQuery('certificate/check',
                ['code' => $code, 'pin' => $pin],
                [],
                function($data) use (&$result) {
                    $result = $data;
                },
                function(\Exception $e) use (&$exception) {
                    $exception = $e;
                    \App::exception()->remove($e);
                }
            );
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['default'], \App::config()->coreV2['retryCount']);
            // TODO SITE-1008
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (!isset($result['status_code'])) {
                throw new \Exception('Не удалось проверить сертификат');
            }

            $statusCode = (int)$result['status_code'];
            if (0 == $statusCode) {
                return new \Http\JsonResponse(['success' => true]);
            } else {
                if (array_key_exists($statusCode, $this->errors)) {
                    $error = $this->errors[$statusCode];
                }
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->warn('Error when checking certificate ' . $e);
            if (743 == $e->getCode()) {
                $error = 'Сертификат не найден';
            }
        }

        return new \Http\JsonResponse(['success' => false, 'error' => $error]);
    }
}