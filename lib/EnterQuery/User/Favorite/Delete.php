<?php

namespace EnterQuery\User\Favorite
{
    use EnterQuery\User\Favorite\Delete\Response;

    class Delete
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
                    'api/favorite/remove',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'uid'      => $this->ui,
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Favorite\Delete
{
    class Response
    {
    }
}