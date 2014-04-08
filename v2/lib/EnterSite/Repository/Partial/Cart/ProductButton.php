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
     * @param Model\Cart\Product|null $cartProduct
     * @return Partial\Cart\ProductButton
     */
    public function getObject(
        Model\Product $product,
        Model\Cart\Product $cartProduct = null
    ) {
        $button = new Partial\Cart\ProductButton();

        $button->dataUrl = $this->router->getUrlByRoute(new Routing\User\Cart\Product\Set());
        $button->dataValue = $this->helper->json([
            'product' => [
                'id'       => $product->id,
                'name'     => $product->name,
                'token'    => $product->token,
                'price'    => $product->price,
                'url'      => $product->link,
                'quantity' => 1,
            ],
        ]);

        $button->class = '';
        $button->id = self::getId($product->id);
        $button->text = 'Купить';

        // если товар в корзине
        if ($cartProduct) {
            $button->text = 'В корзине';
            $button->url = '/cart'; // TODO: route
            $button->dataUrl = '';
            $button->class = ' mDisabled';
        } else {
            if ($product->isInShopOnly) {
                $button->class .= ' mShopsOnly';
                $button->text = 'Резерв';
                //$button->url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]); // TODO
                $button->class .= ' jsOneClickButton';
            }

            if (!$product->isBuyable) {
                $button->url = '#';
                $button->class .= ' mDisabled';
                $button->text = $product->isInShopShowroomOnly ? 'На витрине' : 'Недоступен';
            } else if (!$button->url) {
                $button->url = $this->router->getUrlByRoute(new Routing\Cart\SetProduct($product->id));
            }
        }

        return $button;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-cart-product-buyButton-' . $productId;
    }
}