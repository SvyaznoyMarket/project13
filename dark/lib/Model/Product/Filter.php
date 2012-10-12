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

    /**
     * @param FilterEntity[] $filterCollection
     */
    public function __construct(CategoryEntity $category, array $filterCollection) {
        $this->category = $category;
        $this->filters = $filterCollection;
    }

    public function dump() {
        $return = array();

        foreach ($this->filters as $filter) {
            $value = $this->getValue($filter);
            if (!empty($value)) {
                switch ($filter->getTypeId()) {
                    case FilterEntity::TYPE_NUMBER:
                    case FilterEntity::TYPE_SLIDER:
                        if ($filter->getMax() != $value['to'] || $filter->getMin() != $value['from']) {
                            $return[] = array($filter->getFilterId(), 2, $value['from'], $value['to']);
                        }
                        break;
                    default:
                        $return[] = array($filter->getFilterId(), 1, $value);
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

        return $return;
    }

    public function setValues(array $values) {
        $this->values = $values;
    }

    /**
     * @param FilterEntity $filter
     * @return mixed|null
     */
    public function getValue(FilterEntity $filter) {
        if (isset($this->values[$filter->getFilterId()])) {
            return (array)$this->values[$filter->getFilterId()];
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

    public function getFilterCollection() {
        return $this->filters;
    }
}