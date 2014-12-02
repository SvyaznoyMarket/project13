<?php

namespace View\Partial\ProductCategory\V2;

class SelectedFilter {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Filter $productFilter
     * @param $baseUrl
     * @param bool $useBaseUrl
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Filter $productFilter,
        $baseUrl,
        $useBaseUrl = false
    ) {
        $selected = [];
        foreach ($productFilter->dump() as $item) {
            if (!is_array($item)) continue;

            $selected[] = reset($item);
        }

        $filters = $this->getFilterLinks($helper, $productFilter, $selected, $baseUrl, $useBaseUrl);

        return [
            'baseUrl' => $baseUrl,
            'filters' => $filters,
            'filtersCount' => count($filters),
            'values'  => $this->getFilterValues($productFilter, $selected),
        ];
    }

    private function getFilterLinks(\Helper\TemplateHelper $helper, \Model\Product\Filter $productFilter, $selected, $baseUrl, $useBaseUrl) {
        $filterGroups = [];

        foreach ($productFilter->getUngroupedPropertiesV2() as $property) {
            if (!in_array($property->getId(), $selected)) {
                continue;
            }

            $filterGroups[] = ['name' => $property->getName(), 'isGroup' => false, 'links' => $this->getPropertyLinks($helper, $productFilter, $property, $baseUrl, $useBaseUrl)];
        }

        foreach ($productFilter->getGroupedPropertiesV2() as $group) {
            $filterGroup = ['name' => $group->name, 'isGroup' => true, 'properties' => []];

            foreach ($group->properties as $property) {
                if (!in_array($property->getId(), $selected)) {
                    continue;
                }

                $links = $this->getPropertyLinks($helper, $productFilter, $property, $baseUrl, $useBaseUrl);
                if ($links) {
                    if ('shop' === $property->getId()) {
                        $name = '';
                    } else {
                        $name = $property->getName();
                    }

                    $filterGroup['properties'][] = ['name' => $name, 'links' => $links];
                }
            }

            if ($filterGroup['properties']) {
                $filterGroups[] = $filterGroup;
            }
        }

        $filterGroups = array_values($filterGroups);

        return $filterGroups;
    }

    private function getPropertyLinks(\Helper\TemplateHelper $helper, \Model\Product\Filter $productFilter, \Model\Product\Filter\Entity $property, $baseUrl, $useBaseUrl) {
        $value = $productFilter->getValue($property);
        $isPrice = $property->isPrice();

        $links = [];
        switch ($property->getTypeId()) {
            case \Model\Product\Filter\Entity::TYPE_SLIDER:
            case \Model\Product\Filter\Entity::TYPE_NUMBER:
                if (empty($value['from']) && empty($value['to'])) {
                    continue;
                }

                if (!empty($value['from'])) { // SITE-4114 if (isset($value['from']) && !($isEqualNumeric($value['from'], $filter->getMin()))) {
                    $links[] = [
                        'name' => $isPrice ? sprintf('От %sр', $helper->formatPrice($value['from'])) : sprintf('От %s', round($value['from'], 1)),
                        'url'  => $helper->replacedUrl([
                            \View\Name::productCategoryFilter($property, 'from') => null,
                            'ajax'     => null,
                            'page'     => null, // SITE-4511
                        ]),
                    ];
                }

                if (!empty($value['to'])) { // SITE-4114 if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                    $links[] = [
                        'name' => $isPrice ? sprintf('До %sр', $helper->formatPrice($value['to'])) : sprintf('До %s', round($value['to'], 1)),
                        'url'  => $helper->replacedUrl([
                            \View\Name::productCategoryFilter($property, 'to') => null,
                            'ajax'     => null,
                            'page'     => null, // SITE-4511
                        ]),
                    ];
                }

                break;
            case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                if (!is_array($value) || count($value) == 0) {
                    continue;
                }

                foreach ($value as $v) {
                    $links[] = [
                        'name' => (1 == $v) ? 'Да' : 'Нет',
                        'url'  => $helper->replacedUrl([
                            \View\Name::productCategoryFilter($property, $v) => null,
                            'ajax'     => null,
                            'page'     => null, // SITE-4511
                        ]),
                    ];
                }
                break;
            case \Model\Product\Filter\Entity::TYPE_LIST:
                if (!is_array($value) || count($value) == 0) continue;
                foreach ($property->getOption() as $option) {
                    if (false === $valueIndex = array_search($option->getId(), $value)) {
                        continue;
                    }

                    $url = $helper->replacedUrl([
                            \View\Name::productCategoryFilter($property, $option) => null,
                            'ajax'     => null,
                            'page'     => null, // SITE-4511
                        ], null,
                        ('product.category' === \App::request()->attributes->get('route')) ? 'product.category' : null
                    );

                    if ($useBaseUrl && !strpos($url, '?')) {
                        // Используем базовый урл, если нет гет-параметров
                        $url = $baseUrl;
                    };

                    $links[] = [
                        'name' => 'shop' === $property->getId() ? $option->getName() : mb_strtoupper(mb_substr($option->getName(), 0, 1)) . mb_substr($option->getName(), 1),
                        'url'  => $url,
                    ];
                }
                break;
            default:
                continue;
        }

        return $links;
    }

    private function getFilterValues(\Model\Product\Filter $productFilter, $selected) {
        $filterValues = [];

        foreach ($productFilter->getFilterCollection() as $property) {

            if (!in_array($property->getId(), $selected)) {
                continue;
            }

            $value = $productFilter->getValue($property);

            switch ($property->getTypeId()) {
                case \Model\Product\Filter\Entity::TYPE_SLIDER:
                case \Model\Product\Filter\Entity::TYPE_NUMBER:
                    if (empty($value['from']) && empty($value['to'])) {
                        continue;
                    }

                    if (!empty($value['from'])) { // SITE-4114 if (isset($value['from']) && !($isEqualNumeric($value['from'], $filter->getMin()))) {
                        $filterValues[\View\Name::productCategoryFilter($property, 'from')] = $value['from'];
                    }

                    if (!empty($value['to'])) { // SITE-4114 if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                        $filterValues[\View\Name::productCategoryFilter($property, 'to')] = $value['to'];
                    }

                    break;
                case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                    if (!is_array($value) || count($value) == 0) {
                        continue;
                    }

                    foreach ($value as $v) {
                        $filterValues[\View\Name::productCategoryFilter($property, $v)] = $v;
                    }

                    break;
                case \Model\Product\Filter\Entity::TYPE_LIST:
                    if (!is_array($value) || count($value) == 0) {
                        continue;
                    }

                    foreach ($property->getOption() as $option) {
                        if (false === $valueIndex = array_search($option->getId(), $value)) continue;
                        $filterValues[\View\Name::productCategoryFilter($property, $option)] = $value[$valueIndex];
                    }

                    break;
                default:
                    continue;
            }
        }

        return $filterValues;
    }
}