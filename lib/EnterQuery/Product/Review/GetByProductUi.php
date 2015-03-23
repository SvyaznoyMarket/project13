<?php

namespace EnterQuery\Product\Review
{
    use EnterQuery\Product\Review\GetByProductUi\Response;

    class GetByProductUi
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\ScmsQueryTrait;

        /** @var string */
        public $productUi;
        /** @var int */
        public $pageNum;
        /** @var int */
        public $pageSize;
        /** @var Response */
        public $response;

        public function __construct($productUi = null, $pageNum = null, $pageSize = null)
        {
            $this->response = new Response();

            $this->productUi = $productUi;
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
                        'product_ui'   => $this->productUi,
                        'current_page' => $this->pageNum,
                        'page_size'    => $this->pageSize,
                        'type'         => 'user', // TODO: удалить
                    ]
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->reviews = isset($result['review_list'][0]) ? $result['review_list'] : [];
                    $this->response->reviewCount = isset($result['num_reviews']) ? $result['num_reviews'] : null;
                    $this->response->score = isset($result['avg_score']) ? $result['avg_score'] : null;
                    $this->response->starScore = isset($result['avg_star_score']) ? $result['avg_star_score'] : null;

                    return $result; // for cache
                }
            );

            return $this;
        }
    }
}

namespace EnterQuery\Product\Review\GetByProductUi
{
    class Response
    {
        /** @var array */
        public $reviews = [];
        /** @var int */
        public $reviewCount;
        /** @var float */
        public $score;
        /** @var float */
        public $starScore;
    }
}