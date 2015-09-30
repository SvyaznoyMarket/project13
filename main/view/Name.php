<?php

namespace View;

class Name {
    /**
     * @param \Model\Product\Filter\Entity $filter
     * @param \Model\Product\Filter\Option\Entity|string|null $option
     * @return string
     */
    public static function productCategoryFilter(\Model\Product\Filter\Entity $filter, $option = null) {
        switch ($filter->getTypeId()) {
            case \Model\Product\Filter\Entity::TYPE_SLIDER:
            case \Model\Product\Filter\Entity::TYPE_NUMBER:
                return 'f-' . $filter->getId() . (is_scalar($option) ? ('-' . $option) : '');
            case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                return 'f-' . $filter->getId() . (is_scalar($option) ? ('-' . $option) : '');
            case \Model\Product\Filter\Entity::TYPE_LIST:
                if (in_array($filter->getId(), ['shop', 'category']) && !$filter->getIsMultiple()) {
                    return $filter->getId();
                } else if ('label' === $filter->getId() && $option instanceof \Model\Product\Filter\Option\Entity && 'instore' === $option->getToken()) {
                    return $option->getToken(); // TODO SITE-2403 Вернуть фильтр instore
                } else {
                    return 'f-' . $filter->getId() . ($filter->getIsMultiple() ? '-' . \Util\String::slugify($option instanceof \Model\Product\Filter\Option\Entity ? $option->getName() : $option) : '');
                }
            default:
                return 'f-' . $filter->getId();
        }
    }
}