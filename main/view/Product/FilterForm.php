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
        $helper = &$this->page->helper;
        $return = [];
        foreach ($this->productFilter->getFilterCollection() as $filter) {
            $value = $this->productFilter->getValue($filter);
            switch ($filter->getTypeId()) {
                case FilterEntity::TYPE_SLIDER:
                case FilterEntity::TYPE_NUMBER:
                    if (empty($value['from']) && empty($value['to'])) continue;
                    $name = [];
                    $is_price = ($filter->getId() == 'price') ? true : false;

                    $tmp = $filter->getName();
                    $pos = strpos($tmp, ' ');
                    if ($pos) $tmp = substr($tmp, 0, $pos);
                    $name[] =  $tmp . ': '; // подсказка по критерию фильтрации

                    if (isset($value['from']) && !($this->isEqualNumeric($value['from'], $filter->getMin()))) {
                        if ($is_price){
                            $name[] = 'до ' . $helper->formatPrice( intval( $value['from'] ) );
                        }else{
                            $name[] = 'от ' . $this->formatPriceView( $value['from'] );
                        }
                    }
                    if (isset($value['to']) && !($this->isEqualNumeric($value['to'], $filter->getMax()))) {
                        if ($is_price){
                            $name[] = 'до ' . $helper->formatPrice( intval( $value['to'] ) );
                        }else{
                            $name[] = 'до ' . $this->formatPriceView( $value['to'] );
                        }
                    }
                    if (!$name) continue;
                    if ($is_price) $name[] .= 'р.';
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


    private function  formatPriceView($value, $numDecimals = 1)
    {
        $helper = & $this->page->helper;
        if (!$numDecimals) $numDecimals = 1;
        $decimals = $numDecimals * 10;
        //$old_val = $value;

        if (
            is_int($value) ||
            abs(intval($value) - $value) < (1 / $decimals)
        ) {

            return $helper->formatPrice($value, 0);

        }

        return $helper->formatPrice($value, $numDecimals);

    }
}