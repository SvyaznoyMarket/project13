<?php

namespace Controller\Cart;

class CouponAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception|null
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function apply(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $cart = \App::user()->getCart();

        $number = trim((string)$request->get('number'));

        $responseData = [];
        try {
            if (!$number) {
                throw new \Exception\ActionException('Не передан номер карты');
            }

            $coupon = new \Model\Cart\Coupon\Entity();
            $coupon->setNumber($number);

            $cart->setCoupon($coupon);

            foreach ($cart->getCoupons() as $coupon) {
                if ($number === $coupon->getNumber()) {
                    if ($coupon->getError() instanceof \Exception) {
                        throw $coupon->getError();
                    }
                }
            }

            $responseData['success'] = true;

        } catch (\Exception $e) {
            \App::exception()->remove($e);

            $cart->clearCoupons();

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => \Model\Cart\Coupon\Entity::getErrorMessage($e->getCode()) ?: 'Не удалось активировать купон'],
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

        $responseData = [];
        try {
            \App::user()->getCart()->clearCoupons();

            $responseData['success'] = true;
        } catch (\Exception\ActionException $e) {
            \App::exception()->remove($e);

            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => 'Не удалось удалить купон'],
            ];
        }

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse($responseData) : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('cart'));
    }
}