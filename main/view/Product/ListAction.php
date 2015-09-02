<?php

namespace View\Product;

class ListAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Iterator\EntityPager $pager
     * @param array $bannerPlaceholder
     * @param null $buyMethod
     * @param bool $showState
     * @param int $columnCount
     * @param string $view
     * @param array $cartButtonSender
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager,
        array $bannerPlaceholder,
        $buyMethod = null,
        $showState = true,
        $columnCount = 4,
        $view = 'compact',
        array $cartButtonSender = [],
        \Model\Product\Category\Entity $category = null,
        $favoriteProductsByUi = []
    ) {
        /** @var \Model\Product\Entity $product */

        $productData = [];

        if (0 === $pager->count()) { // кол-во всех продуктов в пейджере (результатов выборки)
            // Не нужно ничего отображать, если кол-во товаров в листинге == 0
            return [
                'products' => $productData,
                'productCount' => 0,
            ];
        }

        $cartButtonAction = new \View\Cart\ProductButtonAction();
        $reviewAction = new \View\Product\ReviewCompactAction();
        $showAction = new \View\Product\ShowAction();

        foreach ($pager as $product) {
            $productData[] = $showAction->execute(
                $helper,
                $product,
                $buyMethod,
                $showState,
                $cartButtonAction,
                $reviewAction,
                (3 === $columnCount) ? 'product_350' : 'product_200',
                $cartButtonSender,
                $category,
                isset($favoriteProductsByUi[$product->ui]) ? $favoriteProductsByUi[$product->ui] : null
            );
        }

        // добавляем баннер в листинги, в нужную позицию
        if (!empty($productData) && $bannerPlaceholder && 1 === $pager->getPage()) {
            $bannerPlaceholder['isBanner'] = true;
            $productData = array_merge(array_slice($productData, 0, $bannerPlaceholder['position']), [$bannerPlaceholder], array_slice($productData, $bannerPlaceholder['position']));
        }

        switch ($view) {
            case 'light_with_bottom_description':
                $templateView = [
                    'extraContent' => true,
                    'bottomDescription' => true,
                    'hoverDescription' => false,
                ];
                break;
            case 'light_with_hover_bottom_description':
                $templateView = [
                    'extraContent' => true,
                    'bottomDescription' => true,
                    'hoverDescription' => true,
                ];
                break;
            case 'light_without_description':
                $templateView = [
                    'extraContent' => false,
                    'bottomDescription' => false,
                    'hoverDescription' => false,
                ];
                break;
            default:
                $templateView = [];
                break;
        }

        return [
            'products' => $productData,
            'productCount' => count($productData), // кол-во продуктов на странице с учётом смещений
            'view' => $templateView,
        ];
    }
}