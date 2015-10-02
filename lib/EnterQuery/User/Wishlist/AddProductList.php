<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\AddProductList\Response;

    class AddProductList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var array */
        public $data = [];
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
            $this->data = $data;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/wishlist/add',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'id'       => $this->data['id'],
                    'products' => array_map(function($item) {
                        return [
                            'uid' => $item['productUi'],
                        ];
                    }, $this->data['products']),
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

namespace EnterQuery\User\Wishlist\AddProductList
{
    class Response
    {
    }
}