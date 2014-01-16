<?php

namespace EnterSite\Curl\Query\Product;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Model\Region;

class GetListByIdList extends Query {
    use \EnterSite\Curl\Query\CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param array $ids
     * @param \EnterSite\Model\Region $region
     */
    public function __construct(array $ids, Region $region = null) {
        $params = [
            'select_type' => 'id',
            'id'          => $ids,
        ];
        if ($region) {
            $params['geo_id'] = $region->id;
        }
        $this->url = 'product/get?' . http_build_query($params);

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