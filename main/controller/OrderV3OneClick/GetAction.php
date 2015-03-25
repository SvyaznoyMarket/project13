<?php

namespace Controller\OrderV3OneClick;

class GetAction {

    public function __construct() {
        $this->session = \App::session();
        $this->splitSessionKey = \App::config()->order['oneClickSplitSessionKey'];
        $this->client = \App::coreClientV2();
        $this->user = \App::user();
    }

    /** Main function
     * @param \Http\Request $request
     * @param $accessToken
     * @return \Http\Response
     */
    public function execute(\Http\Request $request, $accessToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $orders = [];
        $productsById = [];

        if ($order = \RepositoryManager::order()->getEntityByAccessToken($accessToken)) {
            $orders[] = $order;

            foreach ($order->getProduct() as $orderProduct) {
                $productsById[$orderProduct->getId()] = null;
            }

            if ((bool)$productsById) {
                foreach (\RepositoryManager::product()->getCollectionById(array_keys($productsById), null, false) as $product) {
                    $productsById[$product->getId()] = $product;
                }
            }

            /** @var \Model\Product\Entity[] $productsById */
            $productsById = array_filter($productsById);
        }

        $result = [
            'page' => \App::closureTemplating()->render('order-v3-1click/__complete-order', [
                'orders'       => $orders,
                'productsById' => $productsById,
            ]),
        ];

        return new \Http\JsonResponse(['result' => $result]);
    }


}