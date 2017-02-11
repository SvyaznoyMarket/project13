<?php

namespace View\Category\Listing\Product\Variations;

class Variation {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $mainProduct
     * @param \Model\Product\Model\Property\Entity $property
     * @param array $cartButtonSender
     * @param string $categoryUi
     * @param int|string $categoryView
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $mainProduct,
        \Model\Product\Model\Property\Entity $property,
        $categoryUi = '',
        array $cartButtonSender = [],
        $categoryView = \Model\Product\Category\Entity::VIEW_COMPACT
    ) {
        return [
            'id' => $property->id,
            'name' => trim($property->name),
            'lowerName' => mb_strtolower(trim($property->name)),
            'values' => array_values(array_filter(array_map(function(\Model\Product\Model\Property\Option\Entity $option) use($mainProduct, $property, $categoryUi, $cartButtonSender, $categoryView) {
                if (!$option->product) {
                    return null;
                }

                return [
                    'name' => trim($option->value),
                    'checked' => $option->product->ui === $mainProduct->ui,
                    'url' => \App::router()->generateUrl('ajax.category.listing.product', [
                        'categoryUi' => $categoryUi,
                        'productUi' => $option->product->ui,
                        'categoryView' => $categoryView,
                        'sender' => $cartButtonSender,
                    ]),
                ];
            }, $property->option))),
        ];
    }
}