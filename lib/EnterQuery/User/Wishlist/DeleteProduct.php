<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\DeleteProduct\Response;

    class DeleteProduct
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
                'id'  => null,
                'uid' => null,
            ];
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/wishlist/remove',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'id'       => $this->data['id'],
                    'uid'      => $this->data['productUi'],
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

namespace EnterQuery\User\Wishlist\DeleteProduct
{
    class Response
    {
    }
}