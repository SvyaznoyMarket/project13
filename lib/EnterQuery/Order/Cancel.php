<?php

namespace EnterQuery\Order
{
    use EnterQuery\Order\Cancel\Response;

    class Cancel
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $id;
        /** @var string */
        public $userToken;
        /** @var Response */
        public $response;

        public function __construct($id = null, $userToken = null)
        {
            $this->response = new Response();

            $this->id = $id;
            $this->userToken = $userToken;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/order/cancel-request',
                    [
                        'id'    => $this->id,
                        'token' => $this->userToken,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->success = isset($result['success']) ? (bool)$result['success'] : null;
                    $this->response->message = isset($result['message']) ? (string)$result['message'] : null;

                    return $result; // for cache
                },
                5,
                [0]
            );

            return $this;
        }
    }
}

namespace EnterQuery\Order\Cancel
{
    class Response
    {
        /** @var bool|null */
        public $success;
        /** @var string|null */
        public $message;
    }
}