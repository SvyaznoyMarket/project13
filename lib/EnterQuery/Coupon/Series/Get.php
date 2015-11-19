<?php

namespace EnterQuery\Coupon\Series
{
    use EnterQuery\Coupon\Series\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $memberType;
        /** @var Response */
        public $response;

        public function __construct($memberType = null)
        {
            $this->response = new Response();

            $this->memberType = $memberType;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [];
            if (null !== $this->memberType) {
                $queryParams['member_type'] = $this->memberType;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/coupon/get',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->couponSeries = isset($result[0]['uid']) ? $result : [];

                    return $result; // for cache
                },
                2,
                [0, 0.05]
            );

            return $this;
        }
    }
}

namespace EnterQuery\Coupon\Series\Get
{
    class Response
    {
        /** @var array */
        public $couponSeries = [];
    }
}