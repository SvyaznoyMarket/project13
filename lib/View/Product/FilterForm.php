<?php

namespace View\Product;

use \Model\Product\Filter\Entity as FilterEntity;

class FilterForm {
    public static $name = 'f';
    /** @var \Model\Product\Filter */
    private $productFilter;
    /** @var \View\Layout */
    private $page;

    public function __construct(\Model\Product\Filter $productFilter) {
        $this->productFilter = $productFilter;
        $this->page = new \View\Layout();
    }

    public function getSelected() {
        $return = array();
        foreach ($this->productFilter->getFilterCollection() as $filter) {
            $value = $this->productFilter->getValue($filter);
            switch ($filter->getTypeId()) {
                case FilterEntity::TYPE_SLIDER:
                case FilterEntity::TYPE_NUMBER:
                    if (empty($value['from']) && empty($value['to'])) continue;
                    $name = array();
                    if (!($this->isEqualNumeric($value['from'], $filter->getMin()))) $name[] = sprintf('от %d', $value['from']);
                    if (!($this->isEqualNumeric($value['to'], $filter->getMax()))) $name[] = sprintf('до %d', $value['to']);
                    if (!$name) continue;
                    if ($filter->getId() == 'price') $name[] .= 'р.';
                    $return[] = array(
                        'type' => $filter->getId() == 'brand' ? 'creator' : 'parameter',
                        'name'  => implode(' ', $name),
                        'url'   => $this->getUrl($filter->getId()),
                        'title' => $filter->getName(),
                    );
                    break;
                case FilterEntity::TYPE_BOOLEAN:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($value as $v) {
                        $return[] = array(
                            'type'  => $filter->getId() == 'brand' ? 'creator' : 'parameter',
                            'name'  => $filter->getName() . ': ' . ($v == 1 ? 'да' : 'нет'),
                            'url'   => $this->getUrl($filter->getId(), $v),
                            'title' => $filter->getName(),
                        );
                    }
                    break;
                case FilterEntity::TYPE_LIST:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($filter->getOption() as $option) {
                        if (in_array($option->getId(), $value)) {
                            $return[] = array(
                                'type'  => $filter->getId() == 'brand' ? 'creator' : 'parameter',
                                'name'  => $option->getName(),
                                'url'   => $this->getUrl($filter->getId(), $option->getId()),
                                'title' => $filter->getName(),
                            );
                        }
                    }
                    break;
                default:
                    continue;
            }
        }

        return $return;
    }

    /**
     * @param  int  $filterId
     * @param  null $value
     * @return string
     */
    private function getUrl($filterId, $value = null) {
        $data = $this->productFilter->getValues();
        if (array_key_exists($filterId, $data)) {
            if (null == $value) {
                unset($data[$filterId]);
            } else foreach ($data[$filterId] as $k => $v) {
                if ($v == $value) {
                    unset($data[$filterId][$k]);
                }
            }
        }

        return $this->page->url('product.category', array(
            'categoryPath'                  => $this->productFilter->getCategory()->getPath(),
            \View\Product\FilterForm::$name => $data,
        ));
    }

    /**
     * @param $first
     * @param $second
     * @return bool
     */
    private function isEqualNumeric($first, $second) {
        $first = $this->page->helper->clearZeroValue((float)$first);
        $second = $this->page->helper->clearZeroValue((float)$second);

        return $first == $second;
    }
}