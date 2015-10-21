<?php

namespace View\Category\Listing\Product\Variations;

class Variation {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $mainProduct
     * @param \Model\Product\Model\Property\Entity $property
     * @param array $cartButtonSender
     * @param string $categoryUi
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $mainProduct,
        \Model\Product\Model\Property\Entity $property,
        $categoryUi = '',
        array $cartButtonSender = []
    ) {
        return [
            'id' => $property->id,
            'name' => $property->name,
            'lowerName' => mb_strtolower($property->name),
            'url' => \App::router()->generate('ajax.category.listing.product.variation', [
                'categoryUi' => $categoryUi,
                'productUi' => $mainProduct->ui,
                'variationId' => $property->id,
                'cartButtonSender' => $cartButtonSender,
            ]),
            'values' => array_values(array_filter(array_map(function(\Model\Product\Model\Property\Option\Entity $option) use($mainProduct, $property, $categoryUi, $cartButtonSender) {
                if (!$option->product) {
                    return null;
                }

                return [
                    'name' => $option->value,
                    'fieldName' => 'variation[' . $mainProduct->ui . '][' . $option->product->ui . ']',
                    'checked' => $option->product->ui == $mainProduct->ui,
                    'url' => \App::router()->generate('ajax.category.listing.product', [
                        'categoryUi' => $categoryUi,
                        'productUi' => $option->product->ui,
                        'cartButtonSender' => $cartButtonSender,
                    ]),
                ];
            }, $property->option))),
        ];
    }
}