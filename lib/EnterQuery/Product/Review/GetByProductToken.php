<?php

namespace EnterQuery\Product\Review
{
    use EnterQuery\Product\Review\GetByProductToken\Response;

    class GetByProductToken
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $productToken;
        /** @var int */
        public $pageNum;
        /** @var int */
        public $pageSize;
        /** @var Response */
        public $response;

        public function __construct($productToken = null, $pageNum = null, $pageSize = null)
        {
            $this->response = new Response();

            $this->productToken = $productToken;
            $this->pageNum = $pageNum;
            $this->pageSize = $pageSize;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'reviews/list',
                    [
                        'product_slug' => $this->productToken,
                        'current_page' => $this->pageNum,
                        'page_size'    => $this->pageSize,
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->reviews = isset($result['review_list'][0]) ? $result['review_list'] : [];
                    $this->response->reviewCount = isset($result['num_reviews']) ? $result['num_reviews'] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Review\GetByProductToken
{
    class Response
    {
        /** @var array|null */
        public $reviews;
        /** @var int */
        public $reviewCount;
    }
}