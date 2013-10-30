<?php

namespace Enter\Site\Curl\Query\Product\Category;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\CoreQueryTrait;

class GetItemByToken extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     */
    public function __construct($token) {
        $this->url = 'category/get?' . http_build_query([
            'slug' => [$token],
        ]);

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