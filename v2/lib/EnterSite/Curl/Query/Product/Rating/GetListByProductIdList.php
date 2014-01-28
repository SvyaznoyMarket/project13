<?php

namespace EnterSite\Curl\Query\Product\Rating;

use Enter\Curl\Query;
use EnterSite\Curl\Query\ReviewQueryTrait;

class GetListByProductIdList extends Query {
    use ReviewQueryTrait;

    /** @var array|null */
    protected $result;

    public function __construct(array $productIds) {
        $params = [
            'product_list' => implode(',', $productIds),
        ];

        $this->url = 'scores-list?' . http_build_query($params);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['product_scores'][0]['product_id']) ? $data['product_scores'] : [];
    }
}