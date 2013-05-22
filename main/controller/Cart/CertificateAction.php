<?php

namespace Controller\Cart;

class CertificateAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\ActionException
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function apply(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $client = \App::coreClientV2();
        $cart = \App::user()->getCart();

        $number = trim((string)$request->get('number'));

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

    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\JsonResponse
     */
    public function delete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        try {
            \App::user()->getCart()->clearCertificates();

            $result = [
                'success' => true,
            ];
        } catch (\Exception\ActionException $e) {
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