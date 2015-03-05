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
         * @param \Exception $error
         * @return $this
         */
        public function prepare(\Exception &$error = null)
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
                0.5, // timeout multiplier
                $error,
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

namespace EnterQuery\Product\Review\GetByProductUi
{
    class Response
    {
        /** @var array */
        public $reviews = [];
        /** @var int */
        public $reviewCount;
    }
}