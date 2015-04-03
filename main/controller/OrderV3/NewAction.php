<?php

namespace Controller\OrderV3;

use Http\RedirectResponse;
use \Model\OrderDelivery\ValidateException;

class NewAction extends OrderV3 {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
//        $controller = parent::execute($request);
//        if ($controller) {
//            return $controller;
//        }

        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\OrderV3\NewPage();
        $post = null;

        try {

            if ($request->isMethod('POST')) {
                $post = $request->request->all();
                $shop =  null;
                if (method_exists($this->cart, 'getShop')) $shop = $this->cart->getShop();
                $firstDelivery = (new DeliveryAction())->getSplit(null, $shop);
                if ($firstDelivery->errors) $this->session->flash($firstDelivery->errors);
                $delivery = (new DeliveryAction())->getSplit($post);

                // залогируем первичное время доставки
                if ($delivery instanceof \Model\OrderDelivery\Entity && (bool)$delivery->orders) {
                    $deliveryDates = [];
                    $deliveryMethods = [];
                    foreach ($delivery->orders as $order) {
                        if ($order->delivery && $order->delivery->date instanceof \DateTime) $deliveryDates[] = $order->delivery->date->format('Y-n-d');
                        if ($order->delivery) $deliveryMethods[] = $order->delivery->delivery_method->token;
                    }
                    if ((bool)$deliveryDates)  $this->logger(['delivery-dates' => $deliveryDates]);
                    if ((bool)$deliveryMethods)  $this->logger(['delivery-tokens' => $deliveryMethods]);
                }

                switch ($request->attributes->get('route')) {
                    case 'orderV3.one-click': return new RedirectResponse(\App::router()->generate('orderV3.delivery.one-click'));
                    default: return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
                }
            }

            $this->logger(['action' => 'view-page-new']);
            $this->getLastOrderData();

            $this->session->remove($this->splitSessionKey);

            // testing purpose only
            if (\App::config()->debug) (new DeliveryAction())->getSplit();

        } catch (ValidateException $e) {
            $page->setParam('error', $e->getMessage());
        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);

            $page = $e->getCode() == 759 ? new \View\OrderV3\NewPage() : new \View\OrderV3\ErrorPage();

            $page->setParam('error', $e->getMessage());

            $page->setParam('step', 1);

        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);

            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 1);

            return new \Http\Response($page->show(), 500);
        }

        $cart = \App::user()->getCart();
        $bonusCards = (new \Model\Order\BonusCard\Repository($this->client))->getCollection(['product_list' => array_map(function(\Model\Cart\Product\Entity $v) { return ['id' => $v->getId(), 'quantity' => $v->getQuantity()]; }, $cart->getProducts())]);

        $page->setParam('user', $this->user);
        $page->setParam('previousPost', $post);
        $page->setParam('bonusCards', $bonusCards);

        return new \Http\Response($page->show());
    }

    /** Данные о прошлом заказе
     * (оставлено ради совместимости с прошлым оформлением)
     * @return array|null
     */
    public function getLastOrderData() {

        $cookieValue = \App::request()->cookies->get(\App::config()->order['cookieName']);

        if (!empty($cookieValue)) {

            try {
                $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
            } catch (\Exception $e) {
                \App::logger()->error($e, ['unserialize']);
                $cookieValue = [];
            }
        }

        return !empty($cookieValue) ? $cookieValue : null;

    }
}