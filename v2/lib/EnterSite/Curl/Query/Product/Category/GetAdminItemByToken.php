<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\AdminQueryTrait;
use EnterSite\Model\Region;

class GetAdminItemByToken extends Query {
    use AdminQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     * @param \EnterSite\Model\Region $region
     */
    public function __construct($token, Region $region) {
        $this->url = 'category/get-seo?' . http_build_query([
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

        $this->result = isset($data[0]['ui']) ? $data[0] : null;
    }
}