<?php

namespace EnterQuery\Product\Coupon;

use Model\EnterprizeCoupon\Entity as Coupon;

class GetCouponByProductsUi {
    use \EnterQuery\CurlQueryTrait;
    use \EnterQuery\CoreQueryTrait;

    /** @var array */
    public $productUids = [];
    /** @var Response */
    public $response;

    public function __construct($productUids = null)
    {
        $this->response = new Response();
        $this->productUids = (array)$productUids;
        $this->prepare();
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        $this->prepareCurlQuery(
            $this->buildUrl(
                'v2/product/get-product-coupons', []
            ),
            [
                'product_list' => $this->productUids
            ],
            function($response, $statusCode) {
                $result = $this->decodeResponse($response, $statusCode)['result'];
                if (is_array($result)) {
                    foreach ($result as $key => $coupons) {
                        if (is_array($coupons)) {
                            $this->response->couponsByProductUi[$key] = [];
                            foreach ($coupons as $coupon) {
                                $this->response->couponsByProductUi[$key][] = new Coupon($coupon);
                            }
                        }
                    }
                }
                return $result;
            }
        );

        return $this;
    }
}

class Response {

    /** @var array */
    public $couponsByProductUi = [];

    /**
     * @param $productUi
     * @return Coupon[]|null
     */
    public function getCouponsForProduct($productUi) {
        return isset($this->couponsByProductUi[$productUi]) ? $this->couponsByProductUi[$productUi] : null;
    }

}
