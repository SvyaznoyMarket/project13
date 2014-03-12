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
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getOneClickCart();

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

            // запрашиваем список способов оплаты
            /** @var $paymentMethods \Model\PaymentMethod\Entity[] */
            $paymentMethods = [];
            \RepositoryManager::paymentMethod()->prepareCollection(null, $user->getEntity() ? $user->getEntity()->getIsCorporative() : false, function($data) use (
                &$paymentMethods,
                &$user
            ) {
                foreach ($data as $item) {
                    $paymentMethod = new \Model\PaymentMethod\Entity($item);
                    if (!$paymentMethod->getIsCredit()) continue;

                    $paymentMethods[] = $paymentMethod;
                }
            });

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

            $page = new \View\Order\NewPage();
            $page->setParam('paypalECS', false);
            $page->setParam('oneClick', true);
            $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData(false, false, true)); // TODO: пахнет рефакторингом - нужно передавать корзину
            $page->setParam('productsById', $productsById);
            $page->setParam('paymentMethods', $paymentMethods);
            $page->setParam('subways', $subways);
            $page->setParam('banks', $banks);
            $page->setParam('creditData', $creditData);
            $page->setParam('form', $form);
            $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));

            return new \Http\Response($page->show());

        } catch (\Exception $e) {
            $page = new \View\Order\ErrorPage();
            $page->setParam('exception', $e);

            return new \Http\Response($page->show());
        }
    }
}