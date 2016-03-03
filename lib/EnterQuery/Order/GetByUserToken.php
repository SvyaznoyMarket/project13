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

                    $this->response->orders = isset($result['orders'][0]) ? $result['orders'] : [];
                    $this->response->count = isset($result['total']) ? $result['total'] : null;
                    $this->response->currentCount = isset($result['current_count']) ? $result['current_count'] : null;

                    return $result; // for cache
                },
                5,
                [0, 0.025, 0.05] // иногда отваливается с 402 статусом
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
        /** @var int|null */
        public $currentCount;
    }
}