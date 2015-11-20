<?php

namespace EnterQuery\Coupon
{
    use EnterQuery\Coupon\GetByUserToken\Response;

    class GetByUserToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $userToken;
        /** @var Response */
        public $response;

        public function __construct($userToken = null)
        {
            $this->response = new Response();

            $this->userToken = $userToken;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/user/get-discount-coupons',
                    [
                        'token' => $this->userToken,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->coupons = isset($result['detail'][0]) ? $result['detail'] : [];

                    return $result; // for cache
                },
                2,
                [0, 0.05]
            );

            return $this;
        }
    }
}

namespace EnterQuery\Coupon\GetByUserToken
{
    class Response
    {
        /** @var array */
        public $coupons = [];
    }
}