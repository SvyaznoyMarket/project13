<?php

namespace Enter\Site\Curl\Query\Product\Category;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\AdminQueryTrait;

class GetAdminItemByToken extends Query {
    use AdminQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     */
    public function __construct($token) {
        $this->url = 'category/get-seo?' . http_build_query([
            'slug' => [$token],
        ]);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data[0]['ui']) ? $data[0] : null;
    }
}