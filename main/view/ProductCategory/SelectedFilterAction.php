<?php

namespace View\ProductCategory;

class SelectedFilterAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Filter $productFilter
     * @param $baseUrl
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Filter $productFilter,
        $baseUrl
    ) {
        $selected = [];
        foreach ($productFilter->dump() as $item) {
            if (!is_array($item)) continue;

            $selected[] = reset($item);
        }

        $isEqualNumeric = function ($first, $second) use (&$helper) {
            $first = $helper->clearZeroValue((float)$first);
            $second = $helper->clearZeroValue((float)$second);

            return $first == $second;
        };

        $filterData = [];

        $shop = $helper->getParam('shop') && \App::config()->shop['enabled'] ? $helper->getParam('shop') : null;
        if ($shop instanceof \Model\Shop\Entity) {
            $filterData['shop'] = [
                'name'  => 'Наличие в магазинах',
                'links' => [
                    ['name' => sprintf('Только товары из магазина <strong>%s</strong>', $shop->getAddress()), 'url' => $helper->replacedUrl(['page' => null, 'shop' => null, 'ajax' => null])],
                ],
            ];
        }

        foreach ($productFilter->getFilterCollection() as $filter) {

            if (!in_array($filter->getId(), $selected)) {
                continue;
            }

            $value = $productFilter->getValue($filter);
            $isPrice = $filter->isPrice();

            $links = [];
            switch ($filter->getTypeId()) {
                case \Model\Product\Filter\Entity::TYPE_SLIDER:
                case \Model\Product\Filter\Entity::TYPE_NUMBER:
                    if (empty($value['from']) && empty($value['to'])) continue;

                    if (isset($value['from']) && !($isEqualNumeric($value['from'], $filter->getMin()))) {
                        $links[] = [
                            'name' => $isPrice ? sprintf('от %sр', $helper->formatPrice($value['from'])) : sprintf('от %s', round($value['from'], 1)),
                            'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-from' => null, 'ajax' => null]),
                        ];
                    }
                    if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                        $links[] = [
                            'name' => $isPrice ? sprintf('до %sр', $helper->formatPrice($value['to'])) : sprintf('до %s', round($value['to'], 1)),
                            'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-to' => null, 'ajax' => null]),
                        ];
                    }

                    break;
                case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($value as $v) {
                        $links[] = [
                            'name' => ($v == 1) ? 'да' : 'нет',
                            'url'  => $helper->replacedUrl(['f- ' . $filter->getId() => null, 'ajax' => null]),
                        ];
                    }
                    break;
                case \Model\Product\Filter\Entity::TYPE_LIST:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($filter->getOption() as $option) {
                        if (!in_array($option->getId(), $value)) continue;
                        $links[] = [
                            'name' => $option->getName(),
                            'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-' . \Util\String::slugify($option->getName()) => null, 'ajax' => null]),
                        ];
                    }
                    break;
                default:
                    continue;
            }

            if (!(bool)$links) continue;

            if (!isset($filterData[$filter->getId()])) {
                $filterData[$filter->getId()] = ['name' => $filter->getName(), 'links' => []];
            }
            $filterData[$filter->getId()]['links'] += $links;
        }

        $filterData = array_values($filterData);

        $filterItem = end($filterData);
        if (is_array($filterItem)) {
            $filterItem['links'][] = [
                'name' => 'Очистить все',
                'url'  => $baseUrl,
                'last' => true,
            ];
            $filterData[key($filterData)] = $filterItem;
        }

        return [
            'filters' => $filterData,
        ];
    }
}