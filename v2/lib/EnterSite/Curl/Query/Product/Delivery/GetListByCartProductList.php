<?php

namespace EnterSite\Curl\Query\Product\Delivery;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetListByCartProductList extends Query {
    use CoreQueryTrait;

    /** @var array */
    protected $result;

    /**
     * @param Model\Cart\Product[] $cartProducts
     * @param Model\Region $region
     */
    public function __construct(array $cartProducts, Model\Region $region = null) {
        $this->url = new Url();
        $this->url->path = 'v2/delivery/calc';
        if ($region) {
            $this->url->query['geo_id'] = $region->id;
        }
        $this->data['product_list'] = array_map(function(Model\Cart\Product $cartProduct) {
            return ['id' => $cartProduct->id, 'quantity' => $cartProduct->quantity];
        }, $cartProducts);

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = (isset($data['product_list']) && is_array($data['product_list'])) ? $data : [];
    }
}