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

            $queryParams = [
                'product_ui'   => $this->productUi,
                'current_page' => $this->pageNum,
                'page_size'    => $this->pageSize,
            ];

            if (\App::user()->getEntity()) $queryParams['user_uid'] = \App::user()->getEntity()->getUi();

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'reviews/list',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode);

                    $this->response->reviews = [];

                    if (isset($result['review_list'][0])) {
                        foreach ($result['review_list'] as $review) {
                            $this->response->reviews[] = new \Model\Review\ReviewEntity($review);
                        }
                    }

                    $this->response->reviewCount = isset($result['num_reviews']) ? $result['num_reviews'] : null;
                    $this->response->score = isset($result['avg_score']) ? $result['avg_score'] : null;
                    $this->response->starScore = isset($result['avg_star_score']) ? $result['avg_star_score'] : null;
                    $this->response->groupedScoreCount = isset($result['num_users_by_score']) ? $result['num_users_by_score'] : [];
                    $this->response->pageCount = isset($result['page_count']) ? $result['page_count'] : null;
                    $this->response->currentPageAvgScore = isset($result['current_page_avg_score']) ? $result['current_page_avg_score'] : null;

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
        /** @var float */
        public $currentPageAvgScore;
        /** @var array */
        public $groupedScoreCount;
        /** @var int */
        public $pageCount;
    }
}