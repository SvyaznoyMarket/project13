<?php


namespace View\Product;


class SmartChoiceAction {

    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $pager
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        $buyMethod = null,
        $cartButtonAction = null
    ) {

        /** @var \Model\Product\Entity $product */

        $productData = [];

        $productData['id']          = $product->getId();
        $productData['article']     = $product->getArticle();
        $productData['link']        = $product->getLink();
        $productData['prefix']      = $product->getPrefix();
        $productData['webname']     = $product->getWebName();
        $productData['image']       = $product->getMainImageUrl('product_500');
        $productData['tagline']     = $product->getTagline();
        $productData['price']       = $helper->formatPrice($product->getPrice());
        $productData['onePrice']    = true;
        $productData['isKit']       = false;

        if ($product->getPriceOld()) {
            $productData['onePrice'] = false;
            $productData['priceOld'] = $helper->formatPrice($product->getPriceOld());

            if (\App::abTest()->isCurrencyDiscountPrice()) {
                $productData['priceSale'] = $helper->formatPrice($product->getPriceOld() - $product->getPrice());
                $productData['priceSaleUnit'] = ' <span class="rubl">p</span>';
            } else {
                $productData['priceSale'] = round((1 - ($product->getPrice() / $product->getPriceOld())) * 100, 0);
                $productData['priceSaleUnit'] = '%';
            }
        }

        // cart
        if ($buyMethod && in_array(strtolower($buyMethod), ['none', 'false'])) {
            $productData['cartButton'] = null;
        } else {
            $productData['cartButton'] = $cartButtonAction ? $cartButtonAction->execute($helper, $product) : null;
        }

        // kit
        if ($product->getKit() && !$product->getIsKitLocked()) {
            $productData['isKit'] = true;
        }

        return $productData;
    }

} 