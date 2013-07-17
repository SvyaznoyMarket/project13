<?php

namespace Controller\Survey;

class Action {
    public function index(\Http\Request $request, $categoryToken = null) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        if (!$request->isMethod('post')) {
            throw new \Exception\NotFoundException('Request is not post http request');
        }

        try {
            if (!$number) {
                throw new \Exception\ActionException('Не передан номер карты');
            }

            $cart->clearCertificates();

            $data = $client->query('cart/check-card-f1', ['number' => $number]);
            if (true !== $data) {
                throw new \Exception\ActionException('Неправильный номер карты');
            }

            $certificate = new \Model\Cart\Certificate\Entity();
            $certificate->setNumber($number);

            $cart->setCertificate($certificate);

            $result = [
                'success' => true,
            ];

        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $result = [
                'success' => false,
                'error'   => $e instanceof \Exception\ActionException ? $e->getMessage() : 'Неудалось активировать карту',
            ];
            if (\App::config()->debug) {
                $result['error'] = $e;
            }
        }

        return new \Http\JsonResponse($result);
    }
}