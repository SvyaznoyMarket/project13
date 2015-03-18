<?php

namespace EnterQuery\Product\Review
{
    use EnterQuery\Product\Review\GetScoreByProductIdList\Response;

    class GetScoreByProductIdList
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string[] */
        public $productIds = [];
        /** @var Response */
        public $response;

        /**
         * @param string[] $productIds
         */
        public function __construct(array $productIds = [])
        {
            $this->response = new Response();

            $this->productIds = $productIds;
        }

        /**
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'reviews/scores-list',
                    [
                        'product_list' => implode(',', $this->productIds),
                    ]
                ),
                [], // data
                $error,
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

namespace EnterQuery\Product\Review\GetScoreByProductIdList
{
    class Response
    {
        /** @var array */
        public $reviews = [];
    }
}