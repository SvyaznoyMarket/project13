<?php

namespace EnterSite\Controller\Order\Quick;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\Controller;
use EnterSite\SessionTrait;
use EnterSite\Repository;

class Index {
    use ConfigTrait, LoggerTrait, SessionTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, SessionTrait;
        LoggerTrait::getLogger insteadof SessionTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $session = $this->getSession();

        $cartProduct = (new Repository\Cart())->getProductObjectByHttpRequest($request);
        if (!$cartProduct) {
            throw new \Exception('Не передан товар');
        }
        if ($cartProduct->quantity <= 0) {
            throw new \Exception('Количество товара должно быть большим нуля');
        }

        // FIXME: заглушка
        $sessionKey = 'user/cart/one-click';

        $cartData = [
            'product' => [
                $cartProduct->id => [
                    'quantity' => $cartProduct->quantity,
                ],
            ],
        ];

        $session->set($sessionKey, $cartData);

        $url = strtr($request->getSchemeAndHttpHost(), [
            'm.'    => '',
            ':8080' => '', //FIXME: костыль для nginx-а
        ]) . '/orders/one-click/new';

        return (new Controller\Redirect())->execute($url, 302);
    }
}