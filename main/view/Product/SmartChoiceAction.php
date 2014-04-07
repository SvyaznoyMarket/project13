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
        \Model\Product\Entity $product
    ) {

        /** @var \Model\Product\Entity $product */

        $productData = [];

        $productData['id']    = $product->getId();
        $productData['link']    = $product->getLink();
        $productData['prefix']  = $product->getPrefix();
        $productData['webname'] = $product->getWebName();
        $productData['image']   = $product->getImageUrl(2);
        $productData['price']   = $helper->formatPrice($product->getPrice());

        if ($product->getPriceOld()) {
            $productData['priceOld'] = $helper->formatPrice($product->getPriceOld());
            $productItem['priceSale'] = round((1 - ($product->getPrice() / $product->getPriceOld())) * 100, 0);
        }

        return $productData;

    }

} 