<?php

namespace View\Product;

class Variations {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product
    ) {
        $result = [];
        if ($product->getModel() && $product->getModel()->getProperty()) {
            foreach ($product->getModel()->getProperty() as $property) {
                $result[] = (new \View\Product\Variation())->execute($helper, $product, $property);
            }
        }

        return $result;
    }
}