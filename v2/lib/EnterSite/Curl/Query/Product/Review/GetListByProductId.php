<?php

namespace EnterSite\Curl\Query\Product\Review;

use Enter\Curl\Query;
use EnterSite\Curl\Query\ReviewQueryTrait;
use EnterSite\Curl\Query\Url;

class GetListByProductId extends Query {
    use ReviewQueryTrait;

    /** @var array */
    protected $result;

    public function __construct($productId, $pageNum, $itemsPerPage) {
        $this->url = new Url();
        $this->url->path = 'list';
        $this->url->query = [
            'product_id'   => $productId,
            'current_page' => $pageNum,
            'page_size'    => $itemsPerPage,
            //'type'         => 'user',
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['review_list'][0]['product_id']) ? $data['review_list'] : [];
    }
}