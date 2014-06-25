<?php

namespace Coupon;

class CouponManager {
    /** @var string */
    private $cartSessionName;
    /** @var string */
    private $paramName;

    public function __construct() {
        $this->cartSessionName = \App::config()->cart['sessionName'];
        $this->paramName = \App::config()->flocktoryCoupon['paramName'];
    }

    /**
     * @param \Http\Response $response
     */
    public function set(\Http\Response $response = null) {
        $request = \App::request();
        $session = \App::session();
        $cart = $session->get($this->cartSessionName, []);

        // пишем в сессию купон от flocktory
        if (
            $request->get($this->paramName) && isset($cart['couponList']) &&
            \App::config()->flocktoryCoupon['enabled']
        ) {
            $number = $request->get($this->paramName);
            $couponList = $cart['couponList'] && is_array($cart['couponList']) ? $cart['couponList'] : [];

            $is_unique = true;
            foreach ($couponList as $coupon) {
                if (!isset($coupon['number']) || empty($coupon['number'])) {
                    continue;
                }

                if ($number == $coupon['number']) {
                    $is_unique = false;
                }
            }

            if ($is_unique) {
                $couponList[] = ['number' => $number];
                $cart['couponList'] = $couponList;
                $session->set($this->cartSessionName, $cart);
            }
        }
    }
}