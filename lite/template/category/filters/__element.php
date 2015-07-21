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
            echo $helper->render('category/filters/_number', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
        case \Model\Product\Filter\Entity::TYPE_SLIDER:
            echo $helper->render('category/filters/_slider', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
        case \Model\Product\Filter\Entity::TYPE_LIST:
            echo $helper->render('category/filters/_list', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
        case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
            echo $helper->render('category/filters/_choice', ['productFilter' => $productFilter, 'filter' => $filter]);
            break;
    }
};