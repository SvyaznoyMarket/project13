<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetTreeList extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param string|null $regionId
     * @param int|null $maxLevel
     */
    public function __construct($regionId = null, $maxLevel = null) {
        $this->url = new Url();
        $this->url->path = 'v2/category/tree';
        $this->url->query = [
            'is_load_parents' => true,
        ];
        $this->url->query['max_level'] = $maxLevel ?: 6;
        if ($regionId) {
            $this->url->query['region_id'] = $regionId;
        }

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