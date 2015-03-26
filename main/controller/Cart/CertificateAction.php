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
        //\App::logger()->debug('Exec ' . __METHOD__);

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

            $certificate = new \Model\Cart\Certificate\Entity();
            $certificate->setNumber($number);

            $cart->setCertificate($certificate);

            $responseData['success'] = true;

        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => 'Не удалось активировать карту'],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\JsonResponse
     */
    public function delete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $responseData = [];
        try {
            \App::user()->getCart()->clearCertificates();

            $responseData['success'] = true;
        } catch (\Exception\ActionException $e) {
            \App::exception()->remove($e);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => 'Не удалось удалить карту'],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}