<?php

namespace Enter\Site\Curl\Query\Product\Catalog\Config;

use Enter\Curl\Query;
use Enter\Site\Curl\Query\CmsQueryTrait;
use Enter\Site\Model\Product\TreeCategory;

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