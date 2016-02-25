<?php

namespace EnterQuery\Order
{
    use EnterQuery\Order\GetStatusByNumberErp\Response;

    class GetStatusByNumberErp
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $numberErp;
        /** @var Response */
        public $response;

        public function __construct($numberErp = null)
        {
            $this->response = new Response();

            $this->numberErp = $numberErp;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/order/get-status',
                    [
                        'number_erp' => $this->numberErp,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->order = isset($result['order']['status']['id']) ? $result['order'] : null;

                    return $result; // for cache
                },
                2,
                [0, 0.3]
            );

            return $this;
        }
    }
}

namespace EnterQuery\Order\GetStatusByNumberErp
{
    class Response
    {
        /** @var array|null */
        public $order;
    }
}