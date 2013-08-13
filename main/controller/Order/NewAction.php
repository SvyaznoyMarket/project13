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

        // запрашиваем список способов оплаты
        /** @var $paymentMethods \Model\PaymentMethod\Entity[] */
        $paymentMethods = [];
        $creditAllowed = \App::config()->payment['creditEnabled'] && ($user->getCart()->getTotalProductPrice()) >= \App::config()->product['minCreditPrice'];
        \RepositoryManager::paymentMethod()->prepareCollection(null, $user->getEntity() ? $user->getEntity()->getIsCorporative() : false, function($data) use (
            &$paymentMethods,
            $creditAllowed,
            $user
        ) {
            $blockedIds = (array)\App::config()->payment['blockedIds'];

            foreach ($data as $item) {
                $paymentMethod = new \Model\PaymentMethod\Entity($item);
                if (in_array($paymentMethod->getId(), $blockedIds)) continue;

                // кредит
                if ($paymentMethod->getIsCredit() && !$creditAllowed) {
                    continue;
                }
                // подарочный сертификат
                if ($user->getRegion()->getHasTransportCompany() && $paymentMethod->isCertificate()) {
                    continue;
                }

                $paymentMethods[] = $paymentMethod;
            }
        });

        // запрашиваем список кредитных банков
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = [];
        \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks) {
            foreach ($data as $item) {
                $banks[] = new \Model\CreditBank\Entity($item);
            }
        });

        \App::coreClientV2()->execute();

        // данные для кредита
        $creditData = [];
        foreach ($cart->getProducts() as $cartProduct) {
            /** @var $product \Model\Product\CartEntity|null */
            $product = isset($productsById[$cartProduct->getId()]) ? $productsById[$cartProduct->getId()] : null;
            if (!$product) {
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()), ['order']);
                continue;
            }

            $creditData[] = [
                'id'       => $product->getId(),
                'quantity' => $cartProduct->getQuantity(),
                'price'    => $product->getPrice(),
                'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
            ];
        }

        $page = new \View\Order\NewPage();
        $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData());
        $page->setParam('productsById', $productsById);
        $page->setParam('paymentMethods', $paymentMethods);
        $page->setParam('banks', $banks);
        $page->setParam('creditData', $creditData);

        return new \Http\Response($page->show());
    }
}