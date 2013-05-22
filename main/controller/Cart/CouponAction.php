<?php

namespace Controller\Cart;

class CouponAction {
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

            $cart->clearCoupons();

            $data = $client->query('cart/check-coupon', ['number' => $number]);
            if (true !== $data) {
                throw new \Exception\ActionException('Неправильный номер карты');
            }

            $coupon = new \Model\Cart\Coupon\Entity();
            $coupon->setNumber($number);

            $cart->setCoupon($coupon);

            $result = [
                'success' => true,
            ];

        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $result = [
                'success' => false,
                'error'   => $e instanceof \Exception\ActionException ? $e->getMessage() : 'Неудалось активировать купон',
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
            \App::user()->getCart()->clearCoupons();

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