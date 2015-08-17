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
        $response = parent::execute($request);
        if ($response) {
            return $response;
        }

        $page = new \View\OrderV3\NewPage();
        $post = null;

        try {
            if ($request->isMethod('GET')) {
                $this->cart->update([], true);
                $this->cart->markProductsAsInOrder();
                $this->pushEvent(['step' => 1]);
            }

            if ($request->isMethod('POST')) {

                $errors = $this->validateInput($request);
                if ($errors['errors']) {
                    \App::session()->flash($errors);
                    return new RedirectResponse(\App::router()->generate('orderV3'));
                }

                $post = $request->request->all();
                $splitResult = (new DeliveryAction())->getSplit(null, @$post['user_info']);
                if ($splitResult->errors) $this->session->flash($splitResult->errors);

                return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
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

        $bonusCards = (new \Model\Order\BonusCard\Repository($this->client))->getCollection(['product_list' => array_map(function(\Model\Cart\Product\Entity $cartProduct) { return ['id' => $cartProduct->id, 'quantity' => $cartProduct->quantity]; }, $this->cart->getInOrderProductsById())]);

        $page->setParam('user', $this->user);
        $page->setParam('previousPost', $post);
        $page->setParam('bonusCards', $bonusCards);
        $page->setParam('hasProductsOnlyFromPartner', $this->hasProductsOnlyFromPartner());

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
     * @return bool
     */
    private function hasProductsOnlyFromPartner() {
        foreach ($this->cart->getInOrderProductsById() as $cartProduct) {
            if ($cartProduct->isOnlyFromPartner) {
                return true;
            }
        }

        return false;
    }

    private function validateInput(\Http\Request $request){

        $result = ['errors' => [], 'phone' => '', 'email' => ''];

        $post = $request->request->all();
        if (isset($post['user_info']['phone'])) {
            $result['phone'] = $post['user_info']['phone'];
            $phone = preg_replace('/^\+7/', '8', $post['user_info']['phone']);
            $phone = preg_replace('/[\s\(\)-]/', '', $phone);
            if (strlen($phone) != 11) $result['errors'][] = 'Некорректный номер телефона';
        } else {
            $result['errors'][] = 'Не указан номер телефона';
        }
        if (isset($post['user_info']['email'])) {
            $result['email'] = $post['user_info']['email'];
            if (!filter_var($post['user_info']['email'], FILTER_VALIDATE_EMAIL)) {
                $result['errors'][] = 'Некорректный email';
            }
        } else {
            $result['errors'][] = 'Не указан email';
        }

        return $result;
    }
}