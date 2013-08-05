<?php

namespace Controller\Cart;

class BlackcardAction {
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

            $cart->clearBlackcards();

            /*
            $client->query();
            */

            $blackcard = new \Model\Cart\Blackcard\Entity();
            $blackcard->setNumber($number);

            $cart->setBlackcard($blackcard);

            $result = [
                'success' => true,
            ];

        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $message = \Model\Cart\Blackcard\Entity::getErrorMessage($e->getCode()) ?: 'Неудалось активировать карту';

            $result = [
                'success' => false,
                'error'   => (\App::config()->debug ? sprintf('Ошибка #%s: ', $e->getCode()) : '') . $message,
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
            \App::user()->getCart()->clearBlackcards();

            $result = [
                'success' => true,
            ];
        } catch (\Exception\ActionException $e) {
            \App::exception()->remove($e);

            $result = [
                'success' => false,
                'error'   => $e instanceof \Exception\ActionException ? $e->getMessage() : 'Неудалось удалить карту',
            ];
            if (\App::config()->debug) {
                $result['error'] = $e;
            }
        }

        return new \Http\JsonResponse($result);
    }
}