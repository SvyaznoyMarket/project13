<?php

namespace EnterSite\Curl\Query\Cart;

use Enter\Curl\Query;
use EnterSite\Curl\Query\CoreQueryTrait;
use EnterSite\Curl\Query\Url;
use EnterSite\Model;

class GetItem extends Query {
    use CoreQueryTrait;

    /** @var array|null */
    protected $result;

    /**
     * @param Model\Cart $cart
     * @param string $regionId
     */
    public function __construct(Model\Cart $cart, $regionId) {
        $this->url = new Url();
        $this->url->path = 'v2/cart/get-price';
        $this->url->query = [
            'geo_id' => $regionId,
        ];
        $this->data = [
            'product_list'  => array_map(function(Model\Cart\Product $cartProduct) {
                return [
                    'id'       => $cartProduct->id,
                    'quantity' => $cartProduct->quantity,
                ];
            }, $cart->product),
            'service_list'  => [],
            'warranty_list' => [],
        ];

        $this->init();
    }

    /**
     * @param $response
     */
    public function callback($response) {
        $data = $this->parse($response);

        $this->result = isset($data['sum']) ? $data : null;
    }
}