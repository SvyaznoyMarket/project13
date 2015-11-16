<?php

namespace EnterQuery\User\Address
{
    use EnterQuery\User\Address\Delete\Response;

    class Delete
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $id;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param string|null $id
         */
        public function __construct($userUi = null, $id = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->id = $id;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/address/delete',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'id'       => $this->id,
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

namespace EnterQuery\User\Address\Delete
{
    class Response
    {
    }
}