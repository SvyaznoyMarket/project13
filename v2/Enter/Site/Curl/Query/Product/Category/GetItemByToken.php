<?php

namespace Enter\Site\Curl\Query\Product\Category;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\CoreQueryTrait;
use Enter\Site\Model\Region;

class GetItemByToken extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     * @param Region $region
     */
    public function __construct($token, Region $region) {
        $this->url = 'category/get?' . http_build_query([
            'slug'   => [$token],
            'geo_id' => $region->id,
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