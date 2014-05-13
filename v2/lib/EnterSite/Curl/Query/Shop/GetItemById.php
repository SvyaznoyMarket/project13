<?php

namespace EnterSite\Curl\Query\Shop;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetItemById extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param string $id
     */
    public function __construct($id) {
        $this->url = new Url();
        $this->url->path = 'v2/shop/get';
        $this->url->query = [
            'id' => $id,
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data[0]['id']) ? $data[0] : null;
    }
}