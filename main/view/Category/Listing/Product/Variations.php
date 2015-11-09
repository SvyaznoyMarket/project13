<?php

namespace View\Category\Listing\Product;

class Variations {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $mainProduct
     * @param string $categoryUi
     * @param array $cartButtonSender
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $mainProduct,
        $categoryUi = '',
        array $cartButtonSender = []
    ) {
        $result = [];
        if ($mainProduct->model && $mainProduct->model->property) {
            $result[] = (new \View\Category\Listing\Product\Variations\Variation())->execute(
                $helper,
                $mainProduct,
                $mainProduct->model->property,
                $categoryUi,
                $cartButtonSender
            );
        }

        return $result;
    }
}