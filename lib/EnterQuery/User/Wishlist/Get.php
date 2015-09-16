<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\Get\Response;
    use EnterQuery\User\Wishlist\Get\Filter;

    class Get
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        /**
         * @param string|null $userUi
         */
        public function __construct($userUi = null, $filter = null)
        {
            $this->response = new Response();

            $this->userUi = $userUi;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'user_uid' => $this->userUi,
            ];
            if (null !== $this->filter->withProducts) {
                $queryParams['with_products'] = $this->filter->withProducts;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/wishlist',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->wishlists = isset($result[0]['id']) ? $result : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Wishlist\Get
{
    class Response
    {
        /** @var array */
        public $wishlists = [];
    }

    class Filter
    {
        /** @var bool|null */
        public $withProducts;
    }
}