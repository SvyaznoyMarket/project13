<?php

namespace EnterQuery\PaymentMethod
{
    use EnterQuery\PaymentMethod\GetByOrderNumberErp\Response;

    class GetByOrderNumberErp
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string|null */
        public $regionId;
        /** @var string[] */
        public $numberErps = [];
        /** @var Response */
        public $response;

        public function __construct($regionId = null, array $numberErps = [])
        {
            $this->response = new Response();

            $this->regionId = $regionId;
            $this->numberErps = $numberErps;
        }

        /**
         * @return $this
         * @throws \Exception
         */
        public function prepare()
        {
            // валидация
            if (!$this->regionId) {
                throw new \Exception('Не указан регион');
            }

            $queryParams = [
                'geo_id' => $this->regionId,
            ];

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/payment-method/get-for-orders',
                    $queryParams
                ),
                [
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->paymentMethods = is_array($result) ? $result : [];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\PaymentMethod\GetByOrderNumberErp
{
    class Response
    {
        /** @var array */
        public $paymentMethods = [];
    }
}