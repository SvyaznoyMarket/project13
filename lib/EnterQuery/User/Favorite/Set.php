<?php

namespace EnterQuery\User\Favorite
{
    use EnterQuery\User\Favorite\Set\Response;

    class Set
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $ui;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param string|null $ui
         */
        public function __construct($userUi = null, $ui = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->ui = $ui;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/favorite/add',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'uid'      => $this->ui,
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Favorite\Set
{
    class Response
    {
    }
}