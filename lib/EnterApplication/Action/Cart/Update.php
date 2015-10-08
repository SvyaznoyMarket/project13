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

            $cartProductsByUi = $cart->getProductsByUi();
            $productsToUpdate = [];
            foreach ($cartQuery->response->products as $item) {
                if (isset($item['uid']) && isset($item['quantity']) && (!isset($cartProductsByUi[$item['uid']]) || $cartProductsByUi[$item['uid']]->quantity != $item['quantity'])) {
                    $productsToUpdate[] = ['ui' => $item['uid'], 'quantity' => $item['quantity']];
                }
            }

            try {
                $localUpdatedAt = $cart->getCoreUpdated();
                $remoteUpdatedAt = $cartQuery->response->updatedAt ? new \DateTime($cartQuery->response->updatedAt) : null;

                // если получена метка обновления из ядра
                if ($remoteUpdatedAt) {
                    $remoteProductUis = array_column($cartQuery->response->products, 'uid');

                    // если локальные данные устарели, то удаляем товары, которых нет в ядерной корзине
                    if (!$localUpdatedAt || ($localUpdatedAt < $remoteUpdatedAt)) {
                        foreach ($cartProductsByUi as $i => $cartProduct) {
                            if (!in_array($cartProduct->ui, $remoteProductUis)) {
                                $productsToUpdate[] = ['ui' => $cartProduct->ui, 'quantity' => '0'];
                            }
                        }
                    }

                    $cart->setCoreUpdated($remoteUpdatedAt);
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart']);
            }

            try {
                $cart->update($productsToUpdate);
            } catch(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart']);
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