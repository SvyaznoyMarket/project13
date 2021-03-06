<?php

namespace Model\Cart;

use EnterApplication\CurlTrait;
use Session\AbTest\ABHelperTrait;

class Repository {
    use ABHelperTrait, CurlTrait;

    /**
     * @param \Session\Cart\Update\Result\Product[] $updateResultProducts
     */
    public function updateCrmCart($updateResultProducts) {
        $userEntity = \App::user()->getEntity();
        if (!$userEntity) return;

        $this->prepareCrmCartUpdate($updateResultProducts);

        $this->getCurl()->execute();
    }

    /**
     * @param \Session\Cart\Update\Result\Product[] $updateResultProducts
     */
    public function prepareCrmCartUpdate($updateResultProducts) {
        $userEntity = \App::user()->getEntity();
        if (!$userEntity) return;

        foreach ($updateResultProducts as $updateResultProduct) {
            if ($updateResultProduct->setAction === 'delete') {
                (new \EnterQuery\Cart\RemoveProduct($userEntity->getUi(), $updateResultProduct->cartProduct->ui))->prepare();
            } else {
                (new \EnterQuery\Cart\SetProduct($userEntity->getUi(), $updateResultProduct->cartProduct->ui, $updateResultProduct->cartProduct->quantity))->prepare();
            }
        }
    }
}