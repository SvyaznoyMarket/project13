<?php

namespace EnterQuery\Order
{
    use EnterQuery\Order\GetById\Response;

    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $userToken;
        /** @var int */
        public $id;
        /** @var Response */
        public $response;

        public function __construct($userToken = null, $id = null)
        {
            $this->response = new Response();

            $this->userToken = $userToken;
            $this->id = $id;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/order/get-limited',
                    [
                        'token' => $this->userToken,
                        'id'    => $this->id,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->order = isset($result['orders'][0]['id']) ? $result['orders'][0] : null;

                    return $result; // for cache
                },
                5,
                [0, 0.025, 0.05] // иногда отваливается с 402 статусом
            );

            return $this;
        }
    }
}

namespace EnterQuery\Order\GetById
{
    class Response
    {
        /** @var array|null */
        public $order;
    }
}