<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model\Region;

class GetListByIdList extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param array $ids
     * @param \EnterSite\Model\Region $region
     */
    public function __construct(array $ids, Region $region = null) {
        $this->url = new Url();
        $this->url->path = 'product/get';
        $this->url->query = [
            'select_type' => 'id',
            'id'          => $ids,
        ];
        if ($region) {
            $this->url->query['geo_id'] = $region->id;
        }

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