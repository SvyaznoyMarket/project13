<?php

namespace View\ProductCategory;

class SelectedFilterAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Filter $productFilter
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Filter $productFilter
    ) {
        $selected = [];
        foreach ($productFilter->dump() as $item) {
            if (!is_array($item)) continue;

            $selected[] = reset($item);
        }

        /*$isEqualNumeric = function ($first, $second) use (&$helper) {
            $first = $helper->clearZeroValue((float)$first);
            $second = $helper->clearZeroValue((float)$second);

            return $first == $second;
        };*/

        $sort = \App::request()->query->get('sort');

        $filterLinkData = [];
        $filterValueData = [];

        $shop = ($helper->getParam('shop') && \App::config()->shop['enabled']) ? $helper->getParam('shop') : null;
        if ($shop instanceof \Model\Shop\Entity) {
            $filterLinkData['shop'] = [
                'name'  => 'Наличие в магазинах',
                'links' => [
                    [
                        'name' => sprintf('Только товары из магазина <strong>%s</strong>', $shop->getName()),
                        'url' => $helper->replacedUrl(['shop' => null, 'page' => null, 'ajax' => null, 'sort' => $sort])
                    ],
                ],
            ];
            $filterValueData['shop'] = $shop->getId();
        }

        $category = $helper->getParam('selectedCategory') ? $helper->getParam('selectedCategory') : null;
        if ($category instanceof \Model\Product\Category\Entity) {
            $filterLinkData['category'] = [
                'name'  => 'Товары по категориям',
                'links' => [
                    ['name' => $category->getName(), 'url' => $helper->replacedUrl(['category' => null, 'page' => null, 'ajax' => null, 'sort' => $sort])],
                ],
            ];
            $filterValueData['category'] = $category->getId();
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

                    if (!empty($value['from'])) { // SITE-4114 if (isset($value['from']) && !($isEqualNumeric($value['from'], $filter->getMin()))) {
                        $paramName = \View\Name::productCategoryFilter($filter, 'from');
                        $links[] = [
                            'name' =>
                                $isPrice
                                ? sprintf('от %s&nbsp&nbsp<span class="rubl">p</span>', $helper->formatPrice($value['from']))
                                : sprintf('от %s', round($value['from'], 1))
                            ,
                            'unit' => !$isPrice ? $filter->getUnit() : null,
                            'url'  => $helper->replacedUrl([
                                $paramName => null,
                                'ajax'     => null,
                                'page'     => null, // SITE-4511
                                'sort'     => $sort,
                            ]),
                        ];
                        $filterValueData[$paramName] = $value['from'];
                    }
                    if (!empty($value['to'])) { // SITE-4114 if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                        $paramName = \View\Name::productCategoryFilter($filter, 'to');
                        $links[] = [
                            'name' =>
                                $isPrice
                                ? sprintf('до %s&nbsp&nbsp<span class="rubl">p</span>', $helper->formatPrice($value['to']))
                                : sprintf('до %s', round($value['to'], 1))
                            ,
                            'unit' => !$isPrice ? $filter->getUnit() : null,
                            'url'  => $helper->replacedUrl([
                                $paramName => null,
                                'ajax'     => null,
                                'page'     => null, // SITE-4511
                                'sort'     => $sort,
                            ]),
                        ];
                        $filterValueData[$paramName] = $value['to'];
                    }

                    break;
                case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($value as $v) {
                        $paramName = \View\Name::productCategoryFilter($filter, $v);
                        $links[] = [
                            'name' => (1 == $v) ? 'да' : 'нет',
                            'url'  => $helper->replacedUrl([
                                $paramName => null,
                                'ajax'     => null,
                                'page'     => null, // SITE-4511
                                'sort'     => $sort,
                            ]),
                        ];
                        $filterValueData[$paramName] = $v;
                    }
                    break;
                case \Model\Product\Filter\Entity::TYPE_LIST:
                    if (!is_array($value) || count($value) == 0) continue;
                    foreach ($filter->getOption() as $option) {
                        if (false === $valueIndex = array_search($option->getId(), $value)) continue;
                        $paramName = \View\Name::productCategoryFilter($filter, $option);
                        $links[] = [
                            'name' => $option->getName(),
                            'url'  => $helper->replacedUrl([
                                $paramName => null,
                                'ajax'     => null,
                                'page'     => null, // SITE-4511
                                'sort'     => $sort,
                            ]),
                        ];
                        $filterValueData[$paramName] = $value[$valueIndex];
                    }
                    break;
                default:
                    continue;
            }

            if (!(bool)$links) continue;

            if (!isset($filterLinkData[$filter->getId()])) {
                $filterLinkData[$filter->getId()] = ['name' => $filter->getName(), 'links' => []];
            }
            $filterLinkData[$filter->getId()]['links'] += $links;
        }

        $filterLinkData = array_values($filterLinkData);

        $filterItem = end($filterLinkData);
        if (is_array($filterItem)) {
            $filterItem['last'][] = true;
            $filterLinkData[key($filterLinkData)] = $filterItem;
        }

        return [
            'cleanUrl' => $helper->replacedUrl(
                [
                    'ajax'     => null,
                    'page'     => null, // SITE-4511
                    'sort'     => $sort,
                ],
                [],
                null,
                ['q']
            ),
            'filters' => $filterLinkData,
            // SITE-4825
//            'values'  => $filterValueData,
        ];
    }
}