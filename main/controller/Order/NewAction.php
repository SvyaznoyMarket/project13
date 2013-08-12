<?php

namespace Controller\Order;

class NewAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        foreach ($cart->getProducts() as $cartProduct) {
            $productsById[$cartProduct->getId()] = null;
        }
        \RepositoryManager::product()->prepareCollectionById(array_keys($productsById), $region, function($data) use(&$productsById) {
            foreach ($data as $item) {
                $productsById[$item['id']] = new \Model\Product\Entity($item);
            }
        });

        $page = new \View\Order\NewPage();
        $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData());
        $page->setParam('productsById', $productsById);

        return new \Http\Response($page->show());
    }
}