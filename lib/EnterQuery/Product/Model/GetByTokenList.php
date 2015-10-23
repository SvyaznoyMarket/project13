<?php

namespace EnterQuery\Product\Model
{
    use \EnterQuery\Product\Model\GetByTokenList\Filter;
    use \EnterQuery\Product\Model\GetByTokenList\Response;

    class GetByTokenList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $tokens = [];
        /** @var string */
        public $regionId = '';
        /** @var Filter */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(array $tokens, $regionId)
        {
            $this->tokens = $tokens;
            $this->regionId = $regionId;

            $this->filter = new Filter();
            $this->response = new Response();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/product/get-models',
                    [
                        'slugs' => $this->tokens,
                        'geo_id' => $this->regionId,
                    ]
                ),
                [],
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->products = (isset($result['products']) && is_array($result['products'])) ? array_values($result['products']) : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Model\GetByTokenList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }

    class Filter
    {
    }
}