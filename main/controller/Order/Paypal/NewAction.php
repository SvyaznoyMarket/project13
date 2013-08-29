<?php

namespace Controller\Order\Paypal;

class NewAction {
    use \Controller\Order\FormTrait;
    use \Controller\Order\PaypalTrait;

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

        try {
            // корзина
            $cartProduct = $cart->getPaypalProduct();
            if (!$cartProduct) {
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            $paypalToken = trim((string)$request->get('token'));
            if (!$paypalToken) {
                throw new \Exception\NotFoundException('Не передан параметр token');
            }

            $paypalPayerId = trim((string)$request->get('PayerID'));
            if (!$paypalToken) {
                throw new \Exception\NotFoundException('Не передан параметр PayerID');
            }

            // проверка paypal
            $result = $this->getPaypalCheckout($paypalToken, $paypalPayerId);

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
                    if ($paymentMethod->getId() != \Model\PaymentMethod\Entity::PAYPAL_ID) continue;

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

            // кредитные банки
            /** @var $banks \Model\CreditBank\Entity[] */
            $banks = [];

            \App::coreClientV2()->execute();

            if (!(bool)$productsById) {
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()), ['order', 'paypal']);
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            // метод оплаты по умолчанию
            $paymentMethod = reset($paymentMethods);
            $form->setPaymentMethodId($paymentMethod ? $paymentMethod->getId() : null);

            // данные для кредита
            $creditData = [];

            $page = new \View\Order\NewPage();
            $page->setParam('paypalECS', true);
            $page->setParam('deliveryData', (new \Controller\Order\DeliveryAction())->getResponseData(true));
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