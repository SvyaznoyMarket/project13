<?php

namespace EnterSite\Curl\Query\Promo;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetList extends Query {
    use CoreQueryTrait;

    /** @var array */
    protected $result;

    /**
     * @param string $regionId
     */
    public function __construct($regionId) {
        $this->url = new Url();
        $this->url->path = 'v2/promo/get';
        $this->url->query = [
            'geo_id' => $regionId,
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