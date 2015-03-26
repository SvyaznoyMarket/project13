<?php

namespace Controller\Order\OneClick;

class NewAction {
    use \Controller\Order\FormTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getOneClickCart();

        if ($region && \App::config()->newOrder) {
            $ordersNewTest = \App::abTest()->getTest('orders_new');
            $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
            if (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            ) {
                return new \Http\RedirectResponse(\App::router()->generate('orderV3.one-click'));
            }
        }

        try {
            // корзина
            $cartProducts = $cart->getProducts();
            /** @var $cartProduct \Model\Cart\Product\Entity */
            $cartProduct = reset($cartProducts) ?: null;

            if (!$cartProduct) {
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            // форма
            $form = $this->getForm();

            /** @var $productsById \Model\Product\Entity[] */
            $productsById = [];
            \RepositoryManager::product()->prepareCollectionById([$cartProduct->getId()], $region, function($data) use(&$productsById) {
                foreach ($data as $item) {
                    $productsById[$item['id']] = new \Model\Product\Entity($item);
                }
            });

            // запрашиваем группы способов оплаты
            // TODO а это нужно вообще? one-click оплачивается только кэшем.
            /**
             * @var $paymentGroups \Model\PaymentMethod\Group\Entity[]
             * @var $paymentMethods \Model\PaymentMethod\Entity[]
             */
            $paymentGroups = [];
            $paymentMethods = [];
            \RepositoryManager::paymentGroup()->prepareCollection($region,
                [
                    'is_corporative' => $user->getEntity() ? $user->getEntity()->getIsCorporative() : false,
                    'is_credit'      => false,
                ],
                [],
                function($data) use (
                    &$paymentGroups,
                    &$paymentMethods
                ) {
                    if (!isset($data['detail'])) {
                        return;
                    }

                    foreach ($data['detail'] as $group) {
                        $paymentGroup = new \Model\PaymentMethod\Group\Entity($group);
                        if (!$paymentGroup->getPaymentMethods()) {
                            continue;
                        }

                        // отфильтровываем методы которые нам не подходят
                        $blockedIds = (array)\App::config()->payment['blockedIds'];
                        $filteredMethods = array_filter(
                            $paymentGroup->getPaymentMethods(),
                            function(\Model\PaymentMethod\Entity $method) use ($blockedIds) {
                                // выкидываем заблокированные методы
                                if (in_array($method->getId(), $blockedIds)) return;

                                return $method;
                            }
                        );

                        $paymentGroup->setPaymentMethods($filteredMethods);

                        if (!empty($filteredMethods)) {
                            $paymentGroups[$paymentGroup->getId()] = $paymentGroup;

                            // заполняем отдельно массив $paymentMethods
                            foreach ($filteredMethods as $method) {
                                $paymentMethods[] = $method;
                            }
                        }
                    }
                }
            );

            // запрашиваем список станций метро
            /** @var $subways \Model\Subway\Entity[] */
            $subways = [];

            // запрашиваем список кредитных банков
            /** @var $banks \Model\CreditBank\Entity[] */
            $banks = [];
            \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks) {
                foreach ($data as $item) {
                    $banks[] = new \Model\CreditBank\Entity($item);
                }
            });

            \App::coreClientV2()->execute();

            if (!(bool)$productsById) {
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()), ['order', 'one-click']);
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            // метод оплаты по умолчанию
            $paymentMethod = reset($paymentMethods);
            $form->setPaymentMethodId($paymentMethod ? $paymentMethod->getId() : null);

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

            // получение карт лояльности
            $bonusCards = \RepositoryManager::bonusCard()->getCollection(['product_list' => array_map(function(\Model\Cart\Product\Entity $v) { return ['id' => $v->getId(), 'quantity' => $v->getQuantity()]; }, $cart->getProducts())]);
            $userBonusCards = $user->getEntity() && $user->getEntity()->getBonusCard() ? $user->getEntity()->getBonusCard() : [];

            // массив данных для JS
            $bonusCardsData = \Controller\Order\NewAction::getBonusCardsData($request, $bonusCards, $userBonusCards);

            (new \Controller\OrderV3\OrderV3())->logger(['action' => 'view-old-delivery-one-click']);

            $page = new \View\Order\OneClick\NewPage();
            $page->setParam('cart', $cart);
            $page->setParam('paypalECS', false);
            $page->setParam('oneClick', true);
            $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData(false, false, true)); // TODO: пахнет рефакторингом - нужно передавать корзину
            $page->setParam('productsById', $productsById);
            $page->setParam('paymentMethods', $paymentMethods);
            $page->setParam('paymentGroups', $paymentGroups);
            $page->setParam('subways', $subways);
            $page->setParam('banks', $banks);
            $page->setParam('creditData', $creditData);
            $page->setParam('form', $form);
            $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
            $page->setParam('bonusCards', $bonusCards);
            $page->setParam('bonusCardsData', $bonusCardsData);

            return new \Http\Response($page->show());

        } catch (\Exception $e) {
            $page = new \View\Order\ErrorPage();
            $page->setParam('exception', $e);

            return new \Http\Response($page->show());
        }
    }
}