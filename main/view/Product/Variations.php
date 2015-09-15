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
            foreach ($product->getModel()->getProperty() as $property) {
                $result[] = (new \View\Product\Variation())->execute($helper, $product, $property);
            }

            return $result;
        }

        return null;
    }
}