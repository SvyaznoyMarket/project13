<?php

namespace EnterSite\Repository\Partial\Cart;

use Enter\Routing\Router;
use Enter\Helper;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductQuickButton {
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
     * @return Partial\Cart\ProductQuickButton
     */
    public function getObject(
        Model\Product $product
    ) {
        if (!$product->isBuyable) {
            return null;
        }

        $button = new Partial\Cart\ProductQuickButton();

        $button->url = $this->router->getUrlByRoute(new Routing\Order\Quick\Index(), ['product' => ['id' => $product->id, 'quantity' => 1]]);
        $button->dataUrl = $this->router->getUrlByRoute(new Routing\Order\Quick\Index());
        $button->dataValue = $this->helper->json([
            'product' => [
                'id'       => $product->id,
                'quantity' => 1,
            ],
        ]);

        $button->id = self::getId($product->id);
        $button->widgetId = self::getWidgetId($product->id);

        return $button;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-cart-product-quickButton-' . $productId;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getWidgetId($productId) {
        return self::getId($productId) . '-widget';
    }
}