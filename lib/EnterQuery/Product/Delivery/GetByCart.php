<?php

namespace EnterQuery\Product\Delivery
{

    use EnterQuery\Product\Delivery\GetByCart\Response;
    use EnterQuery\Product\Delivery\GetByCart\Cart;

    class GetByCart {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $cart;
        /** @var string|null */
        public $regionId;
        /** @var Response */
        public $response;

        public function __construct(Cart $cart = null, $regionId = null)
        {
            $this->response = new Response();

            $this->cart = $cart;
            $this->regionId = $regionId;
        }

        /**
         * @param \Exception $error
         * @param callable|null $callback
         * @return $this
         * @throws \Exception
         */
        public function prepare(\Exception &$error = null, $callback = null)
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
                            return ['id' => $product->id, 'quantity' => $product->quantity];
                        },
                        $this->cart->products
                    ),
                ], // data
                2, // timeout multiplier
                $callback,
                $error,
                function($response) {
                    $result = $this->decodeResponse($response)['result'];

                    $this->response->products = (isset($result['product_list']) && is_array($result['product_list'])) ? $result['product_list'] : [];
                    $this->response->intervals = (isset($result['interval_list']) && is_array($result['interval_list'])) ? $result['interval_list'] : [];
                    $this->response->shops = (isset($result['shop_list']) && is_array($result['shop_list'])) ? $result['shop_list'] : [];
                    $this->response->regions = (isset($result['geo_list']) && is_array($result['geo_list'])) ? $result['geo_list'] : [];
                }
            );

            return $this;
        }

        /**
         * @return Cart
         */
        public function createCart()
        {
            return new Cart();
        }
    }
}

namespace EnterQuery\Product\Delivery\GetByCart
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
        public function createProduct($id = null, $quantity = null)
        {
            $product = new Cart\Product();
            $product->id = $id;
            $product->quantity = $quantity;

            return $product;
        }
    }
}

namespace EnterQuery\Product\Delivery\GetByCart\Cart
{
    class Product
    {
        /** @var string */
        public $id;
        /** @var int */
        public $quantity;
    }
}