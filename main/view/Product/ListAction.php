<?php

namespace View\Product;

class ListAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Iterator\EntityPager $pager
     * @param array $productVideosByProduct
     * @param array $bannerPlaceholder
     * @param null $buyMethod
     * @param bool $showState
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager,
        array $productVideosByProduct,
        array $bannerPlaceholder,
        $buyMethod = null,
        $showState = true
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

        $user = \App::user();

        $productButtonAction = new \View\Cart\ProductButtonAction();
        $reviewCompactAction = new \View\Product\ReviewCompactAction();

        foreach ($pager as $product) {
            $productVideos = isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [];
            /** @var $productVideo \Model\Product\Video\Entity|null */
            $productVideo = reset($productVideos);
            /** @var string $model3dExternalUrl */
            $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : null;
            /** @var string $model3dImg */
            $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : null;

            $stateLabel = null;
            if ($product->isInShopOnly()) {
                $stateLabel = ['name' => 'Только в магазинах'];
            } else if ($product->getMainCategory() && $product->getMainCategory()->getIsFurniture() &&
                $product->getState() && $product->getState()->getIsStore()) {
                if (14974 === $user->getRegion()->getId()) {
                    // Для Москвы, SITE-2850
                    $stateLabel = ['name' => 'Товар за три дня'];
                } else {
                    // Для регионов (привозит быстрее, но не за три дня)
                    $stateLabel = ['name' => 'Товар со склада'];
                }
            }

            $productItem = [
                'name'         => $product->getName(),
                'link'         => $product->getLink(),
                'label'        =>
                $product->getLabel()
                    ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                    : null
                ,
                'cartButton'   => [],
                'image'        => $product->getImageUrl(2),
                'price'        => $helper->formatPrice($product->getPrice()),
                'oldPrice'     => null,
                'isBuyable'    => $product->getIsBuyable(),
                'onlyInShop'   => $product->isInShopOnly(),
                'stateLabel'   => $showState ? $stateLabel : null,
                'variations'   =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty()) // TODO: перенести в \View\*Action
                    ? array_map(function(\Model\Product\Model\Property\Entity $property) {
                    return [
                        'name' => $property->getName(),
                    ];
                }, $product->getModel()->getProperty())
                    : null
                ,
                'hasVariations' =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty())
                    ? true
                    : null
                ,
                'hasVideo' => $productVideo && $productVideo->getContent(),
                'has360'   => $model3dExternalUrl || $model3dImg,
                'review'   => $reviewCompactAction->execute($helper, $product),
                'isBanner' => false,
                'line'     =>
                ($line = $product->getLine())
                    ? ['name' => $line->getName(), 'productCount' => $line->getLineCount(), 'link' => $helper->url('product.line', ['lineToken' => $line->getToken()])]
                    : null
            ];

            // oldPrice and priceSale
            if ( $product->getPriceOld() && !$user->getRegion()->getHasTransportCompany() ) {
                $productItem['oldPrice'] = $helper->formatPrice($product->getPriceOld());
                $productItem['priceSale'] = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
            }

            // cart
            if ($buyMethod && in_array(strtolower($buyMethod), ['none', 'false'])) {
                $productItem['cartButton'] = null;
            } else {
                $productItem['cartButton'] = $productButtonAction->execute($helper, $product);
            }

            $productData[] = $productItem;
        }

        // добавляем баннер в листинги, в нужную позицию
        if (!empty($productData) && $bannerPlaceholder && 1 === $pager->getPage()) {
            $bannerPlaceholder['isBanner'] = true;
            $productData = array_merge(array_slice($productData, 0, $bannerPlaceholder['position']), [$bannerPlaceholder], array_slice($productData, $bannerPlaceholder['position']));
        }

        return [
            'products' => $productData,
            'productCount' => count($productData), // кол-во продуктов на странице с учётом смещений
        ];
    }
}