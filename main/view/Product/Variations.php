<?php

namespace View\Product;

class Variations {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @return array|null
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product
    ) {
        if ($product->getModel() && $product->getModel()->getProperty()) {
            $result = [];
            $lastIndex = count($product->getModel()->getProperty()) - 1;
            foreach (array_values($product->getModel()->getProperty()) as $index => $property) {
                /** @var \Model\Product\Model\Property\Entity $property */
                $result[] = [
                    'name' => $property->getName(),
                    'isLast' => $index == $lastIndex,
                ];
            }

            return $result;
        }

        return null;
    }
}