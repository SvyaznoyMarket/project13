<?php

namespace EnterQuery\Cart
{
    use EnterQuery\Cart\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var Response */
        public $response;

        public function __construct($userUi = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/cart',
                    [
                        'user_uid' => $this->userUi,
                    ]
                ),
                [], // data
                function($response, $curlQuery) {
                    $result = $this->decodeResponse($response, $curlQuery)['result'];

                    $this->response->products = isset($result['products'][0]) ? $result['products'] : [];
                    $this->response->updatedAt = !empty($result['updated_at']) ? $result['updated_at'] : null;
                    $this->response->timestamp = !empty($result['timestamp']) ? $result['timestamp'] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Cart\Get
{
    class Response
    {
        /** @var array */
        public $products = [];
        /** @var string|null */
        public $updatedAt;
        /** @var int|null */
        public $timestamp;
    }
}