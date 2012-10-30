<?php

namespace Model\Product;

use \Model\Product\Category\Entity as CategoryEntity;
use \Model\Product\Filter\Entity as FilterEntity;

class Filter {
    /** @var CategoryEntity */
    private $category;
    /** @var FilterEntity[] */
    private $filters = array();
    /** @var array */
    private $values = array();
    private $isGlobal = false;

    /**
     * @param FilterEntity[] $filterCollection
     */
    public function __construct(array $filterCollection, $isGlobal = false) {
        $this->filters = $filterCollection;
        $this->isGlobal = $isGlobal;
    }

    /**
     * @param \Model\Product\Category\Entity $category
     */
    public function setCategory($category = null) {
        if (!($category instanceof \Model\Product\Category\Entity || $category instanceof \Model\Tag\Category\Entity)) {
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
        $return = array();

        foreach ($this->filters as $filter) {
            $value = $this->getValue($filter);
            if (!empty($value)) {
                switch ($filter->getTypeId()) {
                    case FilterEntity::TYPE_NUMBER:
                    case FilterEntity::TYPE_SLIDER:
                        if ($filter->getMax() != $value['to'] || $filter->getMin() != $value['from']) {
                            $return[] = array($filter->getId(), 2, $value['from'], $value['to']);
                        }
                        break;
                    default:
                        $return[] = array($filter->getId(), 1, $value);
                        break;
                }
            }
        }

        if (empty($return)) {
            $return[] = array('is_model', 1, array(true));
        }

        $return[] = array('is_view_list', 1, array(true));

        if ($this->category) {
            $return[] = array('category', 1, $this->category->getId());
        }

        if (array_key_exists('global', $this->values) && $this->values['global']) {
            $return[] = array('is_global_buyable', 1, 1);
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
            return (array)$this->values[$filter->getId()];
        } else {
            return array();
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

    public function isGlobal() {
        return $this->isGlobal;
    }
}