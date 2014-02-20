<?php

namespace EnterSite\Curl\Query\Product\Media\Video;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Curl\Query\Url;

class GetGroupedListByProductIdList extends Query {
    use CmsQueryTrait;

    /** @var array|null */
    protected $result;

    public function __construct($productIds) {
        $this->url = new Url();
        $this->url->path = 'video/product/index.json';
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
                if (empty($item['content'])) continue;

                $this->result[] = ['content' => $item['content'], 'product_id' => $productId];
            }
        }
    }
}