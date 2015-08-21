<?php

namespace EnterQuery\Order
{
    use EnterQuery\Order\GetByUserToken\Response;

    class GetByUserToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $userToken;
        /** @var int */
        public $offset;
        /** @var int */
        public $limit;
        /** @var Response */
        public $response;

        public function __construct($userToken = null, $offset = null, $limit = null)
        {
            $this->response = new Response();

            $this->userToken = $userToken;
            $this->offset = $offset;
            $this->limit = $limit;
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
                        'token'  => $this->userToken,
                        'offset' => $this->offset,
                        'limit'  => $this->limit,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->orders = isset($result['orders'][0]) ? $result['orders'] : null;
                    $this->response->count = isset($result['total']) ? $result['total'] : null;

                    return $result; // for cache
                },
                5
            );

            return $this;
        }
    }
}

namespace EnterQuery\Order\GetByUserToken
{
    class Response
    {
        /** @var array */
        public $orders = [];
        /** @var int|null */
        public $count;
    }
}