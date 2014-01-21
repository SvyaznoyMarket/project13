<?php

namespace EnterSite\Curl\Query\Product\Catalog\Config;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Model\Product\TreeCategory;

class GetItemByProductCategoryObject extends Query {
    use CmsQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $category
     */
    public function __construct(TreeCategory $category) {
        $tokens = [
            $category->token,
        ];
        foreach ($category->child as $child) {
            $tokens[] = $child->token;
        }

        $this->url = 'catalog/' . implode('/', $tokens) . '.json';

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = $data;
    }
}