<?php

namespace EnterQuery\Product\Review
{
    use EnterQuery\Product\Review\GetScoreByProductUiList\Response;

    class GetScoreByProductUiList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $productUis = [];
        /** @var Response */
        public $response;

        /**
         * @param string[] $productUis
         */
        public function __construct(array $productUis = [])
        {
            $this->response = new Response();

            $this->productUis = $productUis;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'reviews/scores-list',
                    [
                        'product_ui' => implode(',', $this->productUis),
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->reviews = isset($result['product_scores'][0]) ? $result['product_scores'] : [];

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Review\GetScoreByProductUiList
{
    class Response
    {
        /** @var array */
        public $reviews = [];
    }
}