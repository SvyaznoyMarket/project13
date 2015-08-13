<?php

namespace EnterApplication\Action\Cart
{
    use EnterApplication\Action\Cart\Update\Request;
    use EnterApplication\Action\Cart\Update\Response;
    use EnterQuery as Query;

    class Update {
        use \EnterApplication\CurlTrait;
        use \EnterApplication\Action\ActionTrait;

        /**
         * @param Request $request
         * @return Response
         */
        public function execute(Request $request)
        {
            $curl = $this->getCurl();
            $cart = \App::user()->getCart();

            // response
            $response = new Response();

            // запрос корзины
            $cartQuery = (new Query\Cart\Get($request->userUi))->prepare();

            $curl->execute();

            if ($cartQuery->error) {
                return $response;
            }

            $externalCartProductsByUi = [];
            foreach ($cartQuery->response->products as $item) {
                if (
                    !isset($item['uid'])
                    || !isset($item['quantity'])
                ) continue;

                $externalCartProductsByUi[$item['uid']] = new \Model\Cart\Product\Entity($item);

                // обновление количества товаров
                $cart->setProductQuantityByUi($item['uid'], $item['quantity']);
            }

            $cartProductDataByUi = [];
            foreach ($cart->getProductData() as $item) {
                if (empty($item['ui'])) continue;

                $cartProductDataByUi[$item['ui']] = $item;
            }

            $productUis = $externalCartProductsByUi ? array_diff(array_keys($externalCartProductsByUi), array_keys($cartProductDataByUi)) : [];

            if ($productUis) {
                $productListQuery = (new Query\Product\GetByUiList($productUis, $request->regionId))->prepare();

                $curl->execute();

                foreach ($productListQuery->response->products as $item) {
                    /** @var \Model\Cart\Product\Entity|null $cartProduct */
                    $cartProduct = (isset($item['ui']) && isset($externalCartProductsByUi[$item['ui']])) ? $externalCartProductsByUi[$item['ui']] : null;
                    if (!$cartProduct) continue;

                    $product = new \Model\Product\Entity($item);
                    $cart->setProduct($product, $cartProduct->getQuantity());
                }
            }

            return $response;
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
        }
    }
}

namespace EnterApplication\Action\Cart\Update
{
    use EnterQuery as Query;

    class Request
    {
        /** @var string */
        public $userUi;
        /** @var string */
        public $regionId;
    }

    class Response
    {
    }
}