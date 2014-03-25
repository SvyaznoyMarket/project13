<?php

namespace Controller\Order;

class NewAction {
    use FormTrait;

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

        if ($cart->isEmpty()) {
            return new \Http\RedirectResponse(\App::router()->generate('cart'));
        }

        $form = $this->getForm();

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
        $isCreditAllowed = \App::config()->payment['creditEnabled'] && ($user->getCart()->getTotalProductPrice()) >= \App::config()->product['minCreditPrice'];
        \RepositoryManager::paymentMethod()->prepareCollection(null, $user->getEntity() ? $user->getEntity()->getIsCorporative() : false, function($data) use (
            &$paymentMethods,
            &$isCreditAllowed,
            &$user
        ) {
            $blockedIds = (array)\App::config()->payment['blockedIds'];

            foreach ($data as $item) {
                $paymentMethod = new \Model\PaymentMethod\Entity($item);
                if (in_array($paymentMethod->getId(), $blockedIds)) continue;

                // кредит
                if ($paymentMethod->getIsCredit() && !$isCreditAllowed) {
                    continue;
                }
                // подарочный сертификат
                if ($user->getRegion()->getHasTransportCompany() && $paymentMethod->isCertificate()) {
                    //continue; // SITE-3074
                }

                $paymentMethods[] = $paymentMethod;
            }
        });

        // запрашиваем список станций метро
        /** @var $subways \Model\Subway\Entity[] */
        $subways = [];
        if ($user->getRegion()->getHasSubway()) {
            \RepositoryManager::subway()->prepareCollectionByRegion($user->getRegion(), function($data) use (&$subways) {
                foreach ($data as $item) {
                    $subways[] = new \Model\Subway\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
            });
        }

        // запрашиваем список кредитных банков
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = [];
        \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks) {
            // Последовательность сортировки банков
            $sortOrder = [
                'REN' => null,
                'tcsbank'=> null,
                'OTP' => null,
            ];
            foreach ($data as $item) {
                if (!isset($item['token'])) continue;
                $token = $item['token'];
                $banks[$token] = new \Model\CreditBank\Entity($item);
            }
            $banks = array_replace($sortOrder, $banks);

            foreach($banks as $token => $item) {
                // На всякий случай проверим и удаляем пустую инфу о банках
                if (!isset($item)) {
                    unset($banks[$token]);
                }
            }
        });

        \App::coreClientV2()->execute();

        $productsById = array_filter($productsById, function ($product) {
            return $product instanceof \Model\Product\BasicEntity;
        });

        // метод оплаты по умолчанию
        if ($request->cookies->get('credit_on') && $isCreditAllowed) { // если пользователь положил товар в корзину со включенной галкой "Купи в кредит", то ...
            foreach ($paymentMethods as $paymentMethod) {
                if ($paymentMethod->getIsCredit()) {
                    $form->setPaymentMethodId($paymentMethod->getId());
                    break;
                }
            }
        } else { // иначе, выбираем первый метод оплаты
            $paymentMethod = reset($paymentMethods);
            $form->setPaymentMethodId($paymentMethod ? $paymentMethod->getId() : null);
        }

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
        $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData(false));
        $page->setParam('productsById', $productsById);
        $page->setParam('paymentMethods', $paymentMethods);
        $page->setParam('subways', $subways);
        $page->setParam('banks', $banks);
        $page->setParam('creditData', $creditData);
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}