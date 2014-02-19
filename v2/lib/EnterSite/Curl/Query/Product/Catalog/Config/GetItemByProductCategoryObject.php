<?php

namespace EnterSite\Curl\Query\Product\Catalog\Config;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CmsQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model\Product\Category;

class GetItemByProductCategoryObject extends Query {
    use CmsQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param $category
     */
    public function __construct(Category $category) {
        $tokens = [
            $category->token,
        ];
        foreach ($category->children as $child) {
            $tokens[] = $child->token;
        }

        $this->url = new Url();
        $this->url->path = 'catalog/' . implode('/', $tokens) . '.json';

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