<?php

namespace View\ProductCategory;

class DisabledFilterAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Filter $productFilter
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Filter $productFilter
    ) {
        $filterValueData = [];

        $shop = ($helper->getParam('shop') && \App::config()->shop['enabled']) ? $helper->getParam('shop') : null;
        if ($shop instanceof \Model\Shop\Entity) {
            $filterValueData['shop'] = $shop->getId();
        }

        $category = $helper->getParam('selectedCategory') ? $helper->getParam('selectedCategory') : null;
        if ($category instanceof \Model\Product\Category\Entity) {
            $filterValueData['category'] = $category->getId();
        }

        foreach ($productFilter->getFilterCollection() as $filter) {
            if ($filter->getTypeId() === \Model\Product\Filter\Entity::TYPE_LIST) {
                foreach ($filter->getOption() as $option) {
                    if (0 == $option->getQuantity()) {
                        $paramName = \View\Name::productCategoryFilter($filter, $option);
                        $filterValueData[$paramName] = $option->getId();
                    }

                    continue;
                }
            }
        }

        return [
            'values'  => $filterValueData,
        ];
    }
}