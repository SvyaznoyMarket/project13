<?php

namespace EnterSite\Curl\Query\Product\Media\Video;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Curl\Query\Url;

class GetListByProductId extends Query {
    use CmsQueryTrait;

    /** @var array */
    protected $result;

    public function __construct($productId) {
        $this->url = new Url();
        $this->url->path = sprintf('v1/video/product/%s.json', $productId);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = [];
        foreach ($data as $item) {
            if (!is_array($item)) continue;

            $this->result[] = $item;
        }
    }
}