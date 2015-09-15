<?php

namespace View\Product;

class Variation {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @param \Model\Product\Model\Property\Entity $property
     * @return array|null
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        \Model\Product\Model\Property\Entity $property
    ) {
        return [
            'id' => $property->getId(),
            'name' => $property->getName(),
            'lowerName' => mb_strtolower($property->getName()),
            'values' => array_values(array_map(function(\Model\Product\Model\Property\Option\Entity $option) use($product) {
                return [
                    'name' => $option->getHumanizedName(),
                    'checked' => $option->product->id == $product->id,
                    'product' => [
                        'id' => $option->product->id,
                        'ui' => $option->product->ui,
                    ],
                ];
            }, $property->getOption())),
            'variation' => [
                'product' => [
                    'ui' => $product->ui,
                ],
            ],
        ];
    }
}