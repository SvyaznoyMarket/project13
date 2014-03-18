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

    public function __construct(Model\Region $region = null, $maxLevel = null) {
        $this->url = new Url();
        $this->url->path = 'v2/category/tree';
        $this->url->query = [
            'is_load_parents' => true,
        ];
        if (null !== $maxLevel) {
            $this->url->query['max_level'] = $maxLevel;
        }
        if ($region) {
            $this->url->query['region_id'] = $region->id;
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