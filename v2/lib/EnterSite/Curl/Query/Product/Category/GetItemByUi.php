<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetItemByUi extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $ui
     * @param $regionId
     */
    public function __construct($ui, $regionId) {
        $this->url = new Url();
        $this->url->path = 'v2/category/get';
        $this->url->query = [
            'ui'     => [$ui],
            'geo_id' => $regionId,
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