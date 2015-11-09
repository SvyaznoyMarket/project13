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
        /** @var bool */
        public $noDiscount;
        /** @var Response */
        public $response;

        public function __construct($regionId = null, array $numberErps = [], $noDiscount = null)
        {
            $this->response = new Response();

            $this->regionId = $regionId;
            $this->numberErps = $numberErps;
            $this->noDiscount = $noDiscount;
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
            if (!is_array($this->numberErps) || !$this->numberErps) {
                throw new \Exception('Не указаны номера erp заказов');
            }

            $queryParams = [
                'geo_id'     => $this->regionId,
                'number_erp' => $this->numberErps,
            ];
            if (null !== $this->noDiscount) {
                $queryParams['no_discount'] = $this->noDiscount;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/payment-method/get-for-orders',
                    $queryParams
                ),
                [
                ], // data
                function($response, $statusCode) {
                    $result = (array)$this->decodeResponse($response, $statusCode)['result'];

                    foreach ($result as $orderNumberErp => $item) {
                        $this->response->paymentMethodsByOrderNumberErp[$orderNumberErp] =
                            isset($item['methods'][0])
                            ? $item['methods']
                            : []
                        ;

                        $this->response->paymentGroupsByOrderNumberErp[$orderNumberErp] =
                            isset($item['groups'][0])
                            ? $item['groups']
                            : []
                        ;
                    }


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
        public $paymentMethodsByOrderNumberErp = [];
        /** @var array */
        public $paymentGroupsByOrderNumberErp = [];
    }
}