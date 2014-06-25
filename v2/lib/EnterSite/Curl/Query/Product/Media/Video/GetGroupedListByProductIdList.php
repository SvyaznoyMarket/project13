<?php

namespace EnterSite\Curl\Query\Product\Media\Video;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Curl\Query\Url;

class GetGroupedListByProductIdList extends Query {
    use CmsQueryTrait;

    /** @var array */
    protected $result;

    public function __construct($productIds) {
        $this->url = new Url();
        $this->url->path = 'v1/video/product/index.json';
        $this->data = [
            'id' => $productIds,
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = [];
        foreach ($data as $productId => $itemList) {
            foreach ($itemList as $item) {
                if (!is_array($item)) continue;

                $item['product_id'] = $productId;

                $this->result[] = $item;
            }
        }
    }
}