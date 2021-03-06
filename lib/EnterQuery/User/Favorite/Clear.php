<?php

namespace EnterQuery\User\Favorite
{
    use EnterQuery\User\Favorite\Clear\Response;

    class Clear
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
                    'api/favorite/flush',
                    [
                        'user_uid' => $this->userUi,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                },
                \App::config()->crm['timeout'] / \App::config()->coreV2['timeout'], // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Favorite\Clear
{
    class Response
    {
    }
}