<?php

namespace EnterQuery\Cart
{
    use EnterQuery\Cart\Merge\Response;
    use EnterQuery\Cart\Merge\Cart;

    class Merge
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var Cart */
        public $cart;

        /** @var Response */
        public $response;

        public function __construct($userUi = null, Cart $cart = null)
        {
            $this->response = new Response();

            $this->cart = $cart ?: new Cart();
            $this->userUi = $userUi;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/cart/add-batch',
                    []
                ),
                [
                    'user_uid' => $this->userUi,
                    'products' => array_map(
                        function(Cart\Product $product) {
                            return [
                                'uid'      => $product->ui,
                                'quantity' => $product->quantity,
                            ];
                        },
                        $this->cart->products
                    ),
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->products = isset($result['products'][0]) ? $result['products'] : [];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\Cart\Merge
{
    class Response
    {
        /** @var array */
        public $products = [];
    }

    class Cart
    {
        /** @var Cart\Product[] */
        public $products = [];

        /**
         * @param string|null $ui
         * @param int|null $quantity
         * @return Cart\Product
         */
        public function addProduct($ui = null, $quantity = null)
        {
            $product = new Cart\Product();
            $product->ui = $ui;
            $product->quantity = $quantity;
            $this->products[] = $product;

            return $product;
        }
    }
}

namespace EnterQuery\Cart\Merge\Cart
{
    class Product
    {
        /** @var string */
        public $ui;
        /** @var int */
        public $quantity;
    }
}