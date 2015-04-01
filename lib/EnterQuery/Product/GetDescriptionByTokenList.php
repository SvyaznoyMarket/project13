<?php

namespace EnterQuery\Product
{
    use EnterQuery\Product\GetDescriptionByTokenList\Response;

    class GetDescriptionByTokenList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $tokens = [];
        /** @var Response */
        public $response;

        public function __construct(array $tokens = [], $filter = null)
        {
            $this->response = new Response();

            $this->tokens = $tokens;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'product/get-description/v1',
                    [
                        'slugs'       => $this->tokens,
                        'trustfactor' => true, // TODO: filter
                        'seo'         => true, // TODO: filter
                        'media'       => true, // TODO: filter
                        'property'    => true, // TODO: filter
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->products = (isset($result['products']) && is_array($result['products'])) ? $result['products'] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\GetDescriptionByTokenList
{
    class Response
    {
        /** @var array */
        public $products = [];
    }
}