<?php

namespace EnterQuery\Cart
{
    use EnterQuery\Cart\Clear\Response;

    class Clear
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
                    'api/cart/flush',
                    [
                        'user_uid' => $this->userUi,
                    ]
                ),
                [], // data
                function($response, $curlQuery) {
                    $result = $this->decodeResponse($response, $curlQuery)['result'];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Cart\Clear
{
    class Response
    {
    }
}