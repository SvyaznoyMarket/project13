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
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
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
                $error,
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    return $result; // for cache
                }
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