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
     * @param int|string $categoryView
     * @param array $cartButtonSender
     * @param \Model\Product\Category\Entity|null $category
     * @param array $favoriteProductsByUi
     * @param bool $isWithOrangeBuyButton
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager,
        array $bannerPlaceholder = [],
        $buyMethod = null,
        $showState = true,
        $columnCount = 4,
        $categoryView = \Model\Product\Category\Entity::VIEW_COMPACT,
        array $cartButtonSender = [],
        \Model\Product\Category\Entity $category = null,
        $favoriteProductsByUi = [],
        $isWithOrangeBuyButton = false
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
                isset($favoriteProductsByUi[$product->ui]) ? $favoriteProductsByUi[$product->ui] : null,
                $categoryView
            );
        }

        // добавляем баннер в листинги, в нужную позицию
        if (!empty($productData) && $bannerPlaceholder && 1 === $pager->getPage()) {
            $bannerPlaceholder['isBanner'] = true;
            $productData = array_merge(array_slice($productData, 0, $bannerPlaceholder['position']), [$bannerPlaceholder], array_slice($productData, $bannerPlaceholder['position']));
        }

        return [
            'products' => $productData,
            'productCount' => count($productData),
            'categoryView' => $categoryView,
            'isCompact' => $categoryView == \Model\Product\Category\Entity::VIEW_COMPACT,
            'isExpanded' => $categoryView == \Model\Product\Category\Entity::VIEW_EXPANDED,
            'isLight' => in_array($categoryView, [
                \Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION,
                \Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION,
                \Model\Product\Category\Entity::VIEW_LIGHT_WITHOUT_DESCRIPTION,
            ]),
            'isWithBottomDescription' => in_array($categoryView, [\Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION, \Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION]),
            'isWithHoverDescription' => in_array($categoryView, [\Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION]),
            'isWithOrangeBuyButton' => $isWithOrangeBuyButton,
            'is3Column' => 3 === $columnCount,
            // TODO удалить элемент view через несколько дней после релиза SITE-6700
            'view' => call_user_func(function() use($categoryView) {
                switch ($categoryView) {
                    case \Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION:
                        return [
                            'extraContent' => true,
                            'bottomDescription' => true,
                            'hoverDescription' => false,
                        ];
                    case \Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION:
                        return [
                            'extraContent' => true,
                            'bottomDescription' => true,
                            'hoverDescription' => true,
                        ];
                    case \Model\Product\Category\Entity::VIEW_LIGHT_WITHOUT_DESCRIPTION:
                        return [
                            'extraContent' => false,
                            'bottomDescription' => false,
                            'hoverDescription' => false,
                        ];
                    default:
                        return [];
                }
            }),
        ];
    }
}