<?php

namespace EnterQuery\PaymentGroup
{
    use EnterQuery\PaymentGroup\GetByCart\Response;
    use EnterQuery\PaymentGroup\GetByCart\Cart;
    use EnterQuery\PaymentGroup\GetByCart\Filter;

    class GetByCart
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var Cart */
        public $cart;
        /** @var string|null */
        public $regionId;
        /** @var Filter|null */
        public $filter;
        /** @var Response */
        public $response;

        public function __construct(Cart $cart = null, $regionId = null, Filter $filter = null)
        {
            $this->response = new Response();

            $this->cart = $cart ?: new Cart();
            $this->regionId = $regionId;
            $this->filter = $filter ?: new Filter();
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

            $urlQuery = [
                'geo_id' => $this->regionId,
            ];
            if ($this->filter) {
                if (null !== $this->filter->isCredit) $urlQuery['is_credit'] = $this->filter->isCredit;
                if (null !== $this->filter->isOnline) $urlQuery['is_online'] = $this->filter->isOnline;
                if (null !== $this->filter->isPersonal) $urlQuery['is_personal'] = $this->filter->isPersonal;
                if (null !== $this->filter->isLegal) $urlQuery['is_legal'] = $this->filter->isLegal;
                if (null !== $this->filter->isCorporative) $urlQuery['is_corporative'] = $this->filter->isCorporative;
                if (null !== $this->filter->noDiscount) $urlQuery['no_discount'] = $this->filter->noDiscount;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/payment-method/get-group',
                    $urlQuery
                ),
                [
                    'product_list' => array_map(
                        function(Cart\Product $product) {
                            return ['id' => $product->id, 'quantity' => $product->quantity];
                        },
                        $this->cart->products
                    ),
                ], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->paymentGroups = isset($result['detail'][0]) ? $result['detail'] : [];

                    return $result; // for cache
                },
                1, // timeout ratio
                [0] // delay ratio
            );

            return $this;
        }
    }
}

namespace EnterQuery\PaymentGroup\GetByCart
{
    class Response
    {
        /** @var array */
        public $paymentGroups = [];
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

    class Filter
    {
        /** @var bool */
        public $isCredit;
        /** @var bool */
        public $isOnline;
        /** @var bool */
        public $isPersonal;
        /** @var bool */
        public $isLegal;
        /** @var bool */
        public $isCorporative;
        /** @var bool */
        public $noDiscount;
    }
}

namespace EnterQuery\PaymentGroup\GetByCart\Cart
{
    class Product
    {
        /** @var string */
        public $id;
        /** @var int */
        public $quantity;
    }
}