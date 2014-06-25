<?php

namespace EnterSite\Repository\Partial\Cart;

use Enter\Routing\Router;
use Enter\Helper;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Repository;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSpinner {
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
     * @param int $count
     * @param bool $isDisabled
     * @param bool $hasBuyButton
     * @return Partial\Cart\ProductSpinner
     */
    public function getObject(
        Model\Product $product,
        $count = 1,
        $isDisabled = false,
        $hasBuyButton = true
    ) {
        $spinner = new Partial\Cart\ProductSpinner();

        $spinner->id = self::getId($product->id, $hasBuyButton);
        $spinner->widgetId = self::getWidgetId($product->id, $hasBuyButton);
        $spinner->value = $count;
        $spinner->isDisabled = (bool)$isDisabled;
        $spinner->hasBuyButton = $hasBuyButton;

        if ($hasBuyButton) {
            $spinner->buttonId = Repository\Partial\Cart\ProductButton::getId($product->id);
        } else {
            $spinner->buttonId = self::getId($product->id, $hasBuyButton) . '-input';
            $spinner->dataUrl = $this->router->getUrlByRoute(new Routing\User\Cart\Product\Set());
            $spinner->dataValue = $this->helper->json([
                'product' => [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'token'    => $product->token,
                    'price'    => $product->price,
                    'url'      => $product->link,
                    'quantity' => $count,
                ],
            ]);
        }

        return $spinner;
    }

    /**
     * @param $productId
     * @param bool $hasBuyButton
     * @return string
     */
    public static function getId($productId, $hasBuyButton) {
        return 'id-cart-product-buySpinner' . ($hasBuyButton ? 'WithButton' : '') . '-' . $productId;
    }

    /**
     * @param $productId
     * @param bool $hasBuyButton
     * @return string
     */
    public static function getWidgetId($productId, $hasBuyButton) {
        return self::getId($productId, $hasBuyButton) . '-widget';
    }
}