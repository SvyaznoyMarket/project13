<?php

namespace EnterQuery\User\Wishlist
{
    class GetById
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CrmQueryTrait;

        /** @var string */
        public $userUi;
        /** @var string */
        public $id = '';
        /** @var bool|null */
        public $withProducts = null;
        /** @var \EnterQuery\User\Wishlist\GetById\Response */
        public $response;

        /**
         * @param string $userUi
         * @param string $id
         * @param bool|null $withProducts
         */
        public function __construct($userUi, $id, $withProducts = null)
        {
            $this->response = new \EnterQuery\User\Wishlist\GetById\Response();

            $this->userUi = $userUi;
            $this->id = $id;
            $this->withProducts = $withProducts;
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $queryParams = [
                'user_uid' => $this->userUi,
                'id' => $this->id,
            ];
            if (null !== $this->withProducts) {
                $queryParams['with_products'] = (bool)$this->withProducts;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'api/wishlist',
                    $queryParams
                ),
                [], // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    if (isset($result['id'])) {
                        $this->response->id = (string)$result['id'];
                    }

                    if (isset($result['products']) && is_array($result['products'])) {
                        foreach ($result['products'] as $product) {
                            if (!empty($product['uid'])) {
                                $productModel = new \EnterQuery\User\Wishlist\GetById\Response\Product();
                                $productModel->ui = $product['uid'];
                                $this->response->products[] = $productModel;
                            }
                        }
                    }

                    return $result; // for cache
                },
                \App::config()->crm['timeout'] / \App::config()->coreV2['timeout'], // timeout ratio
                [0]
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Wishlist\GetById
{
    class Response
    {
        /** @var string */
        public $id = '';
        /** @var \EnterQuery\User\Wishlist\GetById\Response\Product[] */
        public $products = [];
    }
}

namespace EnterQuery\User\Wishlist\GetById\Response
{
    class Product
    {
        /** @var string */
        public $ui = '';
    }
}