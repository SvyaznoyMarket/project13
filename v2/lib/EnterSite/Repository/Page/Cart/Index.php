<?php

namespace EnterSite\Repository\Page\Cart;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\TranslateHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Cart\Index as Page;

class Index {
    use ConfigTrait, LoggerTrait, RouterTrait, TranslateHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait, TranslateHelperTrait;
    }

    /**
     * @param Page $page
     * @param Index\Request $request
     */
    public function buildObjectByRequest(Page $page, Index\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $config = $this->getConfig();
        $router = $this->getRouter();

        $productCardRepository = new Repository\Partial\Cart\ProductCard();
        $productSpinnerRepository = new Repository\Partial\Cart\ProductSpinner();
        $productDeleteButtonRepository = new Repository\Partial\Cart\ProductDeleteButton();

        $templateDir = $config->mustacheRenderer->templateDir;

        $page->dataModule = 'cart';

        if (count($request->cart)) {
            $page->content->cart = (new Repository\Partial\Cart())->getObject($request->cart, $request->productsById);
        } else {
            $page->content->cart = false;
        }
        $page->content->orderUrl = $router->getUrlByRoute(new Routing\Order\Index());

        foreach (array_reverse($request->cartProducts) as $cartProduct) {
            $product = isset($request->productsById[$cartProduct->id]) ? $request->productsById[$cartProduct->id] : null;
            if (!$product) {
                // TODO: журналирование
                continue;
            }

            $productCard = $productCardRepository->getObject(
                $cartProduct,
                $product,
                $productSpinnerRepository->getObject($product, $cartProduct->quantity, false, false),
                $productDeleteButtonRepository->getObject($product)
            );
            $page->content->productBlock->products[] = $productCard;
        }

        // шаблоны mustache
        foreach ([
            [
                'id'   => 'tpl-cart-productSum',
                'name' => 'partial/cart/productSum',
            ],
            [
                'id'   => 'tpl-cart-total',
                'name' => 'partial/cart/total',
            ],
            [
                'id'   => 'tpl-cart-bar',
                'name' => 'partial/cart/bar',
            ],
        ] as $templateItem) {
            try {
                $template = new Model\Page\DefaultLayout\Template();
                $template->id = $templateItem['id'];
                $template->content = file_get_contents($templateDir . '/' . $templateItem['name'] . '.mustache');

                $page->templates[] = $template;
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['template']]);
            }
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}