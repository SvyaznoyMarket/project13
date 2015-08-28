<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\Create\Response;

    class Create
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var array */
        public $data;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         * @param array $data
         */
        public function __construct($userUi = null, $data = [])
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->data = $data + [
                'title'       => null,
                'description' => null,
            ];
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/wishlist/create',
                    []
                ),
                [
                    'user_uid'    => $this->userUi,
                    'title'       => $this->data['title'],
                    'description' => $this->data['description'],
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

namespace EnterQuery\User\Wishlist\Create
{
    class Response
    {
    }
}