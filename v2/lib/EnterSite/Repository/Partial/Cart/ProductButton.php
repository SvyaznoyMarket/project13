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

    /**
     * @param Model\Product $product
     * @param string|null $sender
     * @return Partial\Cart\ProductButton
     */
    public function getObject(
        Model\Product $product,
        $sender = null
    ) {

        $button = new Partial\Cart\ProductButton();

        $button->data['group'] = $product->id;
        $button->data['upsale'] = htmlspecialchars(json_encode([
            'url'        => $this->router->getUrlByRoute(new Routing\Product\Upsale($product)),
            //'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false, // TODO
        ], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_HEX_APOS), ENT_QUOTES, 'UTF-8');
        $button->class = self::getId($product->id);
        $button->value = 'Купить';

        if ($product->isInShopOnly) {
            $button->inShopOnly = true;
            $button->value = 'Резерв';
            //$button->url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]); // TODO
            $button->class .= ' jsOneClickButton';
        }

        if (!$product->isBuyable) {
            $button->disabled = true;
            $button->url = '#';
            $button->class .= ' jsBuyButton';
            $button->value = $product->isInShopShowroomOnly ? 'На витрине' : 'Недоступен';
        } else if (!$button->url) {
            if ($sender) {
                $sender .= '|' . $product->id;
            }
            $button->url = $this->router->getUrlByRoute(new Routing\Cart\SetProduct($product, 1, $sender));
            $button->class .= ' jsBuyButton';
        }


        return $button;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return sprintf('id-cartButton-product-%s', $productId);
    }
}