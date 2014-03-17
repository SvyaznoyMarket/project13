<?php

namespace EnterSite\Repository\Partial\Cart;

use Enter\Routing\Router;
use Enter\Helper;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductButton {
    use RouterTrait;
    use ViewHelperTrait;

    /** @var Router */
    protected $router;
    /** @var Helper\View */
    protected $helper;

    public function __construct() {
        $this->router = $this->getRouter();
        $this->helper = $this->getViewHelper();
    }

    /**
     * @param Model\Product $product
     * @return Partial\Cart\ProductButton
     */
    public function getObject(
        Model\Product $product
    ) {

        $button = new Partial\Cart\ProductButton();

        $button->data['group'] = $product->id;
        $button->data['upsale'] = $this->helper->json([
            'url'        => $this->router->getUrlByRoute(new Routing\Product\GetUpsale($product->id)),
            //'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false, // TODO
        ]);
        $button->class = self::getId($product->id);
        $button->value = 'Купить';

        if ($product->isInShopOnly) {
            $button->class .= ' mShopsOnly';
            $button->value = 'Резерв';
            //$button->url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]); // TODO
            $button->class .= ' jsOneClickButton';
        }

        if (!$product->isBuyable) {
            $button->url = '#';
            $button->class .= ' jsBuyButton mDisabled';
            $button->value = $product->isInShopShowroomOnly ? 'На витрине' : 'Недоступен';
        } else if (!$button->url) {
            $button->url = $this->router->getUrlByRoute(new Routing\Cart\SetProduct($product->id, 1));
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