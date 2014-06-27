<?php

namespace EnterSite\Repository\Partial;

use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class UserBlock {
    use RouterTrait;

    /**
     * @param Model\Cart $cart
     * @param Model\User|null $user
     * @return Partial\UserBlock
     */
    public function getObject(
        Model\Cart $cart,
        Model\User $user = null
    ) {
        $router = $this->getRouter();

        $userBlock = new Model\Partial\UserBlock();

        if ($user) {
            $userBlock->isUserAuthorized = true;
            $userBlock->userLink->name = $user->firstName ?: $user->lastName;
            $userBlock->userLink->url = $router->getUrlByRoute(new Routing\User\Index());
        } else {
            $userBlock->isUserAuthorized = false;
            $userBlock->userLink->url = $router->getUrlByRoute(new Routing\User\Login());
        }

        $userBlock->isCartNotEmpty = (bool)$cart->product;
        $userBlock->cart->url = $router->getUrlByRoute(new Routing\Cart\Index());
        if ($userBlock->isCartNotEmpty) {
            $userBlock->cart->quantity = count($cart->product);
            $userBlock->cart->shownSum = $cart->sum ? number_format((float)$cart->sum, 0, ',', ' ') : null;
            $userBlock->cart->sum = $cart->sum;
        }

        return $userBlock;
    }
}