<?php

namespace Enter\Site\Curl\Query\Product;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\CoreQueryTrait;

class GetListByIdList extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param array $ids
     * @param $regionId
     */
    public function __construct(array $ids, $regionId) {
        $this->url = 'product/get?' . http_build_query([
            'select_type' => 'id',
            'id'          => $ids,
            'geo_id'      => $regionId,
        ]);

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