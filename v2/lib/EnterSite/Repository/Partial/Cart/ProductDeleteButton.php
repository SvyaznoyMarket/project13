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

class ProductDeleteButton {
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
     * @return Partial\Cart\ProductDeleteButton
     */
    public function getObject(
        Model\Product $product
    ) {
        $button = new Partial\Cart\ProductDeleteButton();

        $button->dataUrl = $this->router->getUrlByRoute(new Routing\User\Cart\Product\Delete());
        $button->dataValue = $this->helper->json([
            'product' => [
                'id'       => $product->id,
                'name'     => $product->name,
                'token'    => $product->token,
                'price'    => $product->price,
                'url'      => $product->link,
                'quantity' => 0,
            ],
        ]);

        $button->class = '';
        $button->id = self::getId($product->id);
        $button->widgetId = self::getWidgetId($product->id);
        $button->url = $this->router->getUrlByRoute(new Routing\Cart\DeleteProduct($product->id));
        $button->spinnerSelector = Repository\Partial\Cart\ProductSpinner::getWidgetId($product->id, false);

        return $button;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-cart-product-deleteButton-' . $productId;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getWidgetId($productId) {
        return self::getId($productId) . '-widget';
    }
}