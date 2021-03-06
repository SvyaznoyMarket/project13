<?php

namespace EnterQuery\User\Favorite
{
    use EnterQuery\User\Favorite\Get\Response;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         */
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
                    'api/favorite',
                    [
                        'user_uid' => $this->userUi,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->products = isset($result['products'][0]) ? $result['products'] : [];

                    return $result; // for cache
                },
                \App::config()->crm['timeout'] / \App::config()->coreV2['timeout'], // timeout ratio
                [0]
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Favorite\Get
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}