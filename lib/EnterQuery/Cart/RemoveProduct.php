<?php

namespace EnterQuery\Cart
{
    use EnterQuery\Cart\RemoveProduct\Response;

    class RemoveProduct
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $ui;
        /** @var string */
        public $quantity;
        /** @var Response */
        public $response;

        public function __construct($userUi = null, $ui = null, $quantity = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->ui = $ui;
            $this->quantity = $quantity;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/cart/remove',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'uid'      => $this->ui,
                    'quantity' => $this->quantity,
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->quantity = isset($result['quantity']) ? $result['quantity'] : null;

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Cart\RemoveProduct
{
    class Response
    {
        /** @var int */
        public $quantity;
    }
}