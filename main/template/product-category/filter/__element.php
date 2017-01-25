<?php
/**
 * @param \Model\Product\Filter\Entity[] $filters
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    switch ($filter->getTypeId()) {
        case \Model\Product\Filter\Entity::TYPE_NUMBER:
        case \Model\Product\Filter\Entity::TYPE_SLIDER:
            echo $helper->render('product-category/filter/element/__slider', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
        case \Model\Product\Filter\Entity::TYPE_LIST:
            echo $helper->render('product-category/filter/element/__list', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
        case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
            echo $helper->render('product-category/filter/element/__choice', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
    }
};