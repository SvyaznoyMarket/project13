<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;

class GetItemByToken extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     * @param $regionId
     */
    public function __construct($token, $regionId) {
        $this->url = 'product/get?' . http_build_query([
            'select_type' => 'slug',
            'slug'        => $token,
            'geo_id'      => $regionId,
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