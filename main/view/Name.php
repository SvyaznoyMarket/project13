<?php

namespace View;

class Name {
    public static function productCategoryFilter(\Model\Product\Filter\Entity $filter, $option = null) {
        switch ($filter->getTypeId()) {
            case \Model\Product\Filter\Entity::TYPE_SLIDER:
            case \Model\Product\Filter\Entity::TYPE_NUMBER:
                return 'f-' . $filter->getId() . (is_scalar($option) ? ('-' . $option) : '');
            case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                return 'f- ' . $filter->getId();
            case \Model\Product\Filter\Entity::TYPE_LIST:
                return ('shop' == $filter->getId())
                    ? 'shop'
                    : ('f-'
                        . $filter->getId()
                        . ($filter->getIsMultiple()
                            ? ('-' . \Util\String::slugify($option instanceof \Model\Product\Filter\Option\Entity ? $option->getName() : $option))
                            : '')
                    );
            default:
                return 'f-' . $filter->getId();
        }
    }
}