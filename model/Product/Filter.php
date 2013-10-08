<?php

namespace Model\Product;

use \Model\Product\Category\Entity as CategoryEntity;
use \Model\Product\Filter\Entity as FilterEntity;

class Filter {
    /** @var CategoryEntity */
    private $category;
    /** @var FilterEntity[] */
    private $filters = [];
    /** @var array */
    private $values = [];
    private $isGlobal = false;
    private $inStore = false;
    /** @var \Model\Shop\Entity[] */
    private $shop;

    /**
     * @param FilterEntity[] $filterCollection
     * @param bool           $isGlobal
     * @param bool           $inStore
     */
    public function __construct(array $filterCollection, $isGlobal = false, $inStore = false, $shop = null) {
        $this->filters = $filterCollection;
        $this->isGlobal = $isGlobal;
        $this->inStore = $inStore;
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
        if (!($category instanceof \Model\Product\Category\BasicEntity || $category instanceof \Model\Tag\Category\Entity)) {
            throw new \InvalidArgumentException('Category must be instance of \Model\Product\Category\Entity or \Model\Tag\Category\Entity');
        }

        $this->category = $category;
    }

    /**
     * @return \Model\Product\Category\Entity
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
                        if ($filter->getMax() != $value['to'] || $filter->getMin() != $value['from']) {
                            $return[] = [$filter->getId(), 2, $value['from'], $value['to']];
                        }
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

        if (array_key_exists('global', $this->values) && $this->values['global']) {
            $return[] = ['is_global_buyable', 1, 1];
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
     * @param array $valuesadmitad_uid
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
     * @return bool
     */
    public function isGlobal() {
        return $this->isGlobal;
    }

    /**
     * @return bool
     */
    public function inStore() {
        return $this->inStore;
    }
}