<?php

namespace EnterSite\Curl\Query\Shop;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetListByIdList extends Query {
    use CoreQueryTrait;

    /** @var array */
    protected $result;

    /**
     * @param array $ids
     */
    public function __construct(array $ids) {
        $this->url = new Url();
        $this->url->path = 'v2/shop/get';
        $this->url->query = [
            'id' => $ids,
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data[0]['id']) ? $data : [];
    }
}