<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Model;

class GetTreeList extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    public function __construct(Model\Region $region = null, $maxLevel = null) {
        $params = [
            'is_load_parents' => true,
        ];
        if (null !== $maxLevel) {
            $params['max_level'] = $maxLevel;
        }
        if ($region) {
            $params['region_id'] = $region->id;
        }

        $this->url = 'category/tree?' . http_build_query($params);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data[0]) ? $data : [];
    }
}