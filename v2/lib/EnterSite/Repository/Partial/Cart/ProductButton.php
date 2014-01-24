<?php

namespace EnterSite\Repository\Partial\Cart;

use Enter\Routing\Router;
use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductButton {
    use RouterTrait;

    /** @var Router */
    protected $router;

    public function __construct() {
        $this->router = $this->getRouter();
    }

    public function getObject(
        Model\Product $product
    ) {

        $button = new Partial\Cart\ProductButton();

        if ($product->isBuyable) {
            $button->url = $this->router->getUrlByRoute(new Routing\Cart\SetProduct($product));
            $button->value = 'Купить';
        } else {
            $button->url = '#';
            $button->value = 'Нет в наличии';
        }

        return $button;
    }
}