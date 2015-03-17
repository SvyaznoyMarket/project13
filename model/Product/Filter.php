<?php

namespace Model\Product;

use \Model\Product\Category\Entity as CategoryEntity;
use \Model\Product\Filter\Entity as FilterEntity;
use Model\Product\Filter\Group;

class Filter {
    /** @var CategoryEntity|null */
    private $category;
    /** @var FilterEntity[] */
    private $filters = [];
    /** @var array */
    private $values = [];
    /** @var \Model\Shop\Entity */
    private $shop;

    /**
     * @param FilterEntity[] $filterCollection
     * @param null           $shop
     */
    public function __construct(array $filterCollection, $shop = null) {
        $this->filters = $filterCollection;
        $this->shop = $shop;
    }

    public function getShop() {
        return $this->shop;
    }

    /**
     * @param \Model\Product\Category\BasicEntity $category
     * @throws \InvalidArgumentException
     */
    public function setCategory($category = null) {
        $this->category = $category;
    }

    /**
     * @return \Model\Product\Category\Entity|null
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return array
     */
    public function dump() {
        $return = [];

        foreach ($this->filters as $filter) {
            $value = $this->getValue($filter);
            if (!empty($value)) {
                switch ($filter->getTypeId()) {
                    case FilterEntity::TYPE_NUMBER:
                    case FilterEntity::TYPE_SLIDER:
                        if (!isset($value['to'])) {
                            $value['to'] = null;
                        }
                        if (!isset($value['from'])) {
                            $value['from'] = null;
                        }

                        $return[] = [$filter->getId(), 2, $value['from'], $value['to']];
                        break;
                    case FilterEntity::TYPE_STRING:
                        $return[] = [$filter->getId(), 3, $value];
                        break;
                    default:
                        $return[] = [$filter->getId(), 1, $value];
                        break;
                }
            }
        }

        if (empty($return)) {
            $return[] = ['is_model', 1, [true]];
        }

        $return[] = ['is_view_list', 1, [true]];

        if ($this->category) {
            $return[] = ['category', 1, $this->category->getId()];
        }

        if (array_key_exists('instore', $this->values) && $this->values['instore']) {
            $return[] = ['is_store', 1, 1];
        }

        if (array_key_exists('shop', $this->values) && $this->values['shop'] && $this->shop && $this->shop instanceof \Model\Shop\Entity) {
            $return[] = ['shop', 1, $this->shop->getId()];
        }

        return $return;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values) {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param FilterEntity $filter
     * @return mixed|null
     */
    public function getValue(FilterEntity $filter) {
        if (isset($this->values[$filter->getId()])) {
            switch ($filter->getTypeId()) {
                case FilterEntity::TYPE_STRING:
                    return $this->values[$filter->getId()];
                default:
                    return (array)$this->values[$filter->getId()];
            }
        } else {
            return [];
        }
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setValue($key, $value) {
        return $this->values[$key] = $value;
    }

    /**
     * @param Filter\Entity $filter
     * @return mixed
     */
    public function getValueMin(FilterEntity $filter) {
        $value = $this->getValue($filter);
        if (isset($value['from'])) {
            return $value['from'];
        } else {
            return $filter->getMin();
        }
    }

    /**
     * @param Filter\Entity $filter
     * @return mixed
     */
    public function getValueMax(FilterEntity $filter) {
        $value = $this->getValue($filter);
        if (isset($value['to'])) {
            return $value['to'];
        } else {
            return $filter->getMax();
        }
    }

    /**
     * @return Filter\Entity[]
     */
    public function getFilterCollection() {
        return $this->filters;
    }

    /**
     * @return Filter\Entity|null
     */
    public function getPriceProperty() {
        foreach ($this->filters as $property) {
            if ($property->isPrice()) {
                return $property;
            }
        }

        return null;
    }

    public function hasInListGroupedProperties() {
        foreach ($this->getGroupedPropertiesV2() as $group) {
            if ($group->hasInListProperties()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Group[]
     */
    public function getGroupedPropertiesV2() {
        $groups = [];
        $shopProperty = null;
        $brandProperty = null;
        $instoreProperty = null;
        $additionalGroup = null;
        foreach ($this->filters as $property) {
            if ($property->isPrice()) {
            } else if ($property->isLabel()) {
            } else if ($property->isBrand()) {
                $brandProperty = $property;
            } else if ('instore' === $property->getId()) {
                $instoreProperty = $property;
            } else if ($property->isShop()) {
                $shopProperty = $property;
            } else if ($property->groupUi) {
                if (isset($groups[$property->groupUi])) {
                    $group = $groups[$property->groupUi];
                } else {
                    $group = new Group();
                    $group->ui = $property->groupUi;
                    $group->name = $property->groupName;
                    $group->position = $property->groupPosition;
                    $groups[$property->groupUi] = $group;

                    if ('Дополнительно' === $property->groupName) {
                        $additionalGroup = $group;
                    }
                }

                $group->properties[] = $property;
                if ($this->getValue($property)) {
                    $group->hasSelectedProperties = true;
                }
            }
        }

        usort($groups, function(Group $a, Group $b) {
            if ($a->position == $b->position) {
                return 0;
            }

            return $a->position < $b->position ? -1 : 1;
        });

        if ($instoreProperty) {
            $group = new Group();
            $group->name = $instoreProperty->getName();
            $group->properties[] = $instoreProperty;
            $group->hasSelectedProperties = (bool)$this->getValue($instoreProperty);
            array_unshift($groups, $group);
        }

        if ($shopProperty) {
            $group = new Group();
            $group->name = $shopProperty->getName();
            $group->properties[] = $shopProperty;
            $group->hasSelectedProperties = (bool)$this->getValue($shopProperty);
            array_unshift($groups, $group);
        }

        if ($brandProperty && !$brandProperty->getIsAlwaysShow()) {
            $group = new Group();
            $group->name = $brandProperty->getName();
            $group->properties[] = $brandProperty;
            $group->hasSelectedProperties = (bool)$this->getValue($brandProperty);
            array_unshift($groups, $group);
        }

        return $groups;
    }

    /**
     * @return Filter\Entity[]
     */
    public function getUngroupedPropertiesV2() {
        $properties = [];

        foreach ($this->filters as $property) {
            if ($property->isPrice()) {
                $properties[] = $property;
            } else if ($property->isLabel()) {
                $properties[] = $property;
            } else if ($property->isBrand() && $property->getIsAlwaysShow()) {
                $properties[] = $property;
            }
        }

        return $properties;
    }
}