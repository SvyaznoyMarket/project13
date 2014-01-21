<?php

namespace EnterSite\Curl\Query\Product\Category;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Model\Region;
use EnterSite\Model\Product\Category;

class GetAncestryItemByCategoryObject extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param \EnterSite\Model\Product\Category $category
     * @param Region $region
     */
    public function __construct(Category $category, Region $region = null) {
        $params = [
            'root_id'         => $category->id,
            'max_level'       => $category->level - 1,
            'is_load_parents' => true,
        ];
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

        $this->result = isset($data[0]['id']) ? $data[0] : null;
    }
}