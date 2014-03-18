<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\AdminQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetAdminItemByToken extends Query {
    use AdminQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $token
     * @param Model\Region $region
     */
    public function __construct($token, Model\Region $region) {
        $this->url = new Url();
        $this->url->path = 'v2/category/get-seo';
        $this->url->query = [
            'slug'   => [$token],
            'geo_id' => $region->id,
        ];

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