<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\Delete\Response;

    class Delete
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var array */
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
                    'api/wishlist/delete',
                    []
                ),
                [
                    'user_uid'    => $this->userUi,
                    'wishlist_id' => $this->id,
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

namespace EnterQuery\User\Wishlist\Delete
{
    class Response
    {
    }
}