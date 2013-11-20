<?php

namespace Controller\Order\LifeGift;

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
        $region = new \Model\Region\Entity(['id' => \App::config()->lifeGift['regionId']]);
        $cart = $user->getLifeGiftCart();

        try {
            if (!\App::config()->lifeGift['enabled']) {
                throw new \Exception('Акция отключена');
            }

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
                    if ($paymentMethod->getPayOnReceipt() != \Model\PaymentMethod\Entity::TYPE_NOW) continue;

                    $paymentMethods[] = $paymentMethod;
                }
            });

            // запрашиваем список станций метро
            /** @var $subways \Model\Subway\Entity[] */
            $subways = [];

            // кредитные банки
            /** @var $banks \Model\CreditBank\Entity[] */
            $banks = [];

            \App::coreClientV2()->execute();

            if (!(bool)$productsById) {
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()), ['order', 'life-gift']);
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            // метод оплаты по умолчанию
            $paymentMethod = reset($paymentMethods);
            $form->setPaymentMethodId($paymentMethod ? $paymentMethod->getId() : null);

            // данные для кредита
            $creditData = [];

            // данные о доставке
            $deliveryData = (new \Controller\Order\DeliveryAction())->getResponseData(false, true); // TODO: пахнет рефакторингом - нужно передавать корзину
            if (isset($deliveryData['deliveryStates']) && is_array($deliveryData['deliveryStates'])) {
                foreach ($deliveryData['deliveryStates'] as &$deliveryState) {
                    $deliveryState['name'] = 'Вы дарите';
                }
                if (isset($deliveryState)) unset($deliveryState);
            }

            $page = new \View\Order\NewPage();
            $page->setParam('paypalECS', false);
            $page->setParam('lifeGift', true);
            $page->setParam('deliveryData', $deliveryData);
            $page->setParam('productsById', $productsById);
            $page->setParam('paymentMethods', $paymentMethods);
            $page->setParam('subways', $subways);
            $page->setParam('banks', $banks);
            $page->setParam('creditData', $creditData);
            $page->setParam('form', $form);

            return new \Http\Response($page->show());

        } catch (\Exception $e) {
            $page = new \View\Order\ErrorPage();
            $page->setParam('exception', $e);

            return new \Http\Response($page->show());
        }
    }
}