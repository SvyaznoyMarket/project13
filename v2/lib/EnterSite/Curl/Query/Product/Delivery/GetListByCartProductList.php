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
     * @param string|null $regionId
     */
    public function __construct(array $cartProducts, $regionId = null) {
        $this->url = new Url();
        $this->url->path = 'v2/delivery/calc';
        if ($regionId) {
            $this->url->query['geo_id'] = $regionId;
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