<?php

namespace Controller\Shop;

class Send {
    /**
     * @param string $pointUi
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute($pointUi, \Http\Request $request) {
        $responseData = [
            'error' => '',
        ];

        try {
            $email = trim($request->request->get('email'));
            if (!$email) {
                throw new \Exception('Не введён email');
            }

            $coreClient = \App::coreClientV2();
            $coreClient->addQuery(
                'notification/send-shop-contacts',
                [
                    'point_ui' => $pointUi,
                    'email'    => $email,
                ],
                [],
                null,
                function(\Exception $e) use(&$exception) {
                    \App::exception()->remove($e);
                    $exception = $e;
                }
            );

            $coreClient->execute();

            if ($exception) {
                throw $exception;
            }
        } catch (\Exception $e) {
            if ($e instanceof \Curl\Exception || $e instanceof \Controller\Exception) {
                $responseData['error'] = $e->getMessage();
            } else {
                $responseData['error'] = 'Не удалось отправить письмо';
            }
        }

        return new \Http\JsonResponse($responseData);
    }
}