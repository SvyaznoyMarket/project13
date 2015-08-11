<?php

namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use Http\RedirectResponse;
use Model\OrderDelivery\ValidateException;
use EnterQuery as Query;

class NewAction extends OrderV3 {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {

        $page = new \View\OrderV3\NewPage();
        $post = null;

        try {
            if ($request->isMethod('GET')) {
                $this->pushEvent(['step' => 1]);
            }

            if ($request->isMethod('POST')) {
                $post = $request->request->all();
                $shop =  null;
                if (method_exists($this->cart, 'getShop')) $shop = $this->cart->getShop();
                $splitResult = (new DeliveryAction())->getSplit(null, $shop, @$post['user_info']);
                if ($splitResult->errors) $this->session->flash($splitResult->errors);

                switch ($request->attributes->get('route')) {
                    case 'orderV3.one-click': return new RedirectResponse(\App::router()->generate('orderV3.delivery.one-click'));
                    default: return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
                }
            }

            $this->getLastOrderData();

            $this->session->remove($this->splitSessionKey);
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
        $page->setParam('hasProductsOnlyFromPartner', $this->hasProductsOnlyFromPartner($cart));

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

    /** Есть ли товары не от Enter?
     * @param \Session\Cart $cart
     * @return bool
     */
    private function hasProductsOnlyFromPartner(\Session\Cart $cart) {
        $ids = array_keys($cart->getProductData());
        $products = (bool)$ids ? \RepositoryManager::product()->getCollectionById($ids, \App::user()->getRegion(), false) : [];
        $productsFromPartner = array_filter($products, function (\Model\Product\Entity $p) { return $p->isOnlyFromPartner() ; });

        return (bool)$productsFromPartner;
    }
}