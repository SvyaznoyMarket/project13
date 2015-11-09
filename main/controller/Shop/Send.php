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
                throw new \Controller\Exception('Не введён email');
            }

            $coreClient = \App::coreClientV2();
            $coreClient->addQuery(
                'notification/send-point-shop-contacts',
                [
                    'email' => $email,
                    'uid'   => $pointUi,
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
            if (($e instanceof \Curl\Exception && in_array($e->getCode(), [850, 737])) || $e instanceof \Controller\Exception) {
                $responseData['error'] = $e->getMessage();
            } else {
                $responseData['error'] = 'Не удалось отправить письмо';
            }
        }

        return new \Http\JsonResponse($responseData);
    }
}