<?php

namespace EnterQuery\User\Wishlist
{
    use EnterQuery\User\Wishlist\GetByToken\Response;
    use EnterQuery\User\Wishlist\GetByToken\Filter;

    class GetByToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $token;
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        /**
         * @param string|null $token
         */
        public function __construct($token = null, $filter = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->filter = $filter ?: new Filter();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'token' => $this->token,
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

                    $this->response->wishlist = isset($result['id']) ? $result : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Wishlist\GetByToken
{
    class Response
    {
        /** @var array|null */
        public $wishlist = [];
    }

    class Filter
    {
        /** @var bool|null */
        public $withProducts;
    }
}