<?php

namespace Controller\OrderV3;

use EnterApplication\CurlTrait;
use Session\AbTest\ABHelperTrait;
use Http\RedirectResponse;
use Model\OrderDelivery\ValidateException;
use EnterQuery as Query;

class NewAction extends OrderV3 {
    use CurlTrait, ABHelperTrait;

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
        $page->setParam('step', 1);
        $post = null;

        $orderDelivery = null;
        try {
            $previousSplit = $this->session->get($this->splitSessionKey);
            if (is_array($previousSplit)) {
                $orderDelivery = new \Model\OrderDelivery\Entity($previousSplit);
            }
        } catch(\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);
        }
        $page->setParam('orderDelivery', $orderDelivery);

        try {
            if ($request->isMethod('GET')) {
                try {
                    $this->cart->update([], true);
                } catch(\Exception $e) {}

                $this->pushEvent(['step' => 1]);
            }

            if ($request->isMethod('POST')) {

                $errors = $this->validateInput($request);
                if ($errors['errors']) {
                    \App::session()->flash($errors);
                    return new RedirectResponse(\App::router()->generateUrl('orderV3'));
                }

                $post = $request->request->all();
                $this->session->set('user_info_split', $post['user_info']);
                $this->session->set($this->splitSessionKey, []);

                return new RedirectResponse(\App::router()->generateUrl('orderV3.delivery'));
            }
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

        $bonusCards = (new \Model\Order\BonusCard\Repository($this->client))->getCollection(['product_list' => array_map(function(\Model\Cart\Product\Entity $cartProduct) { return ['id' => $cartProduct->id, 'quantity' => $cartProduct->quantity]; }, $this->cart->getProductsById())]);

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['checkout_1']) ? $entity->value['checkout_1'] : [];
            }

            return true;
        });

        \App::curl()->execute();

        $page->setParam('user', $this->user);
        $page->setParam('previousPost', $post);
        $page->setParam('bonusCards', $bonusCards);
        $page->setParam('hasProductsOnlyFromPartner', $this->hasProductsOnlyFromPartner());
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }

    public function validateInput(\Http\Request $request){

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

        // валидируем ядром
        $validationResult = $this->validateUserInfo($post['user_info']);

        if (isset($validationResult['error']) && is_array($validationResult['error'])) {
            foreach ($validationResult['error'] as $e) {
                $result['errors'][] = isset($e['message']) && $e['message'] ? $e['message'] : 'Неизвестная ошибка';
            }
        }

        return $result;
    }
}