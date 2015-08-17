<?php

namespace EnterApplication\Action\Cart
{
    use EnterApplication\Action\Cart\Merge\Request;
    use EnterApplication\Action\Cart\Merge\Response;
    use EnterQuery as Query;

    class Merge {
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

            // объединение корзины
            $cartMergeQuery = new Query\Cart\Merge($request->userUi);
            foreach ($cart->getProductsById() as $item) {
                $cartMergeQuery->cart->addProduct($item->ui, $item->quantity);
            }
            $cartMergeQuery->prepare();

            $curl->execute();

            // запрос корзины
            $cartQuery = (new Query\Cart\Get($request->userUi))->prepare();

            $curl->execute();

            $productsToUpdate = [];
            foreach ($cartQuery->response->products as $item) {
                if (isset($item['uid'])) {
                    $productsToUpdate[] = ['ui' => $item['uid'], 'quantity' => isset($item['quantity']) ? $item['quantity'] : 0];
                }
            }
        
            try {
                $cart->update($productsToUpdate);
            } catch(\Exception $e) {
                \App::logger()->error(['message' => 'Не удалось синхронизировать сессионную корзину с серверной корзиной', 'error' => $e, 'sender' => __FILE__ . ' ' . __LINE__], ['cart/update']);
            }

            // response
            $response = new Response();

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

namespace EnterApplication\Action\Cart\Merge
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