<?php

namespace EnterQuery\Delivery
{
    use EnterQuery\Delivery\GetByCart\Response;
    use EnterQuery\Delivery\GetByCart\Cart;

    class GetByCart
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var Cart */
        public $cart;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct(Cart $cart = null, $regionId = null)
        {
            $this->response = new Response();

            $this->cart = $cart ?: new Cart();
            $this->regionId = $regionId;
        }

        /**
         * @return $this
         * @throws \Exception
         */
        public function prepare()
        {
            // валидация
            if (!$this->regionId) {
                throw new \Exception('Не указан регион');
            }
            if (!$this->cart || !$this->cart->products) {
                throw new \Exception('Не указана корзина');
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/delivery/calc2',
                    [
                        'geo_id' => $this->regionId,
                    ]
                ),
                [
                    'product_list' => array_map(
                        function(Cart\Product $product) {
                            return [
                                'id'       => (int)$product->id, // FIXME: int для кеша
                                'quantity' => $product->quantity
                            ];
                        },
                        $this->cart->products
                    ),
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->products = (isset($result['product_list']) && is_array($result['product_list'])) ? $result['product_list'] : [];
                    $this->response->intervals = (isset($result['interval_list']) && is_array($result['interval_list'])) ? $result['interval_list'] : [];
                    $this->response->shops = (isset($result['shop_list']) && is_array($result['shop_list'])) ? $result['shop_list'] : [];
                    $this->response->regions = (isset($result['geo_list']) && is_array($result['geo_list'])) ? $result['geo_list'] : [];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0, 0.08] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Delivery\GetByCart
{
    class Response
    {
        /** @var array */
        public $products = [];
        /** @var array */
        public $intervals = [];
        /** @var array */
        public $shops = [];
        /** @var array */
        public $regions = [];
    }

    class Cart
    {
        /** @var Cart\Product[] */
        public $products = [];

        /**
         * @param string|null $id
         * @param int|null $quantity
         * @return Cart\Product
         */
        public function addProduct($id = null, $quantity = null)
        {
            $product = new Cart\Product();
            $product->id = $id;
            $product->quantity = $quantity;
            $this->products[] = $product;

            return $product;
        }
    }
}

namespace EnterQuery\Delivery\GetByCart\Cart
{
    class Product
    {
        /** @var string */
        public $id;
        /** @var int */
        public $quantity;
    }
}