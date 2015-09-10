<?php

namespace View\Product;

class Properties {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @return array|null
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product
    ) {
        $result = [];
        $properties = $product->getPropertiesInView(5);
        $lastIndex = count($properties) - 1;
        foreach (array_values($properties) as $index => $property) {
            /** @var \Model\Product\Property\Entity $property */
            $result[] = [
                'name' => $property->getName(),
                'value' => $property->getStringValue(),
                'isLast' => $index == $lastIndex,
            ];
        }

        return $result;
    }
}