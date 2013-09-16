<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter
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

    $getUrl = function($filterId, $value = null) use (&$helper, &$productFilter) {
        $data = $productFilter->getValues();
        if (array_key_exists($filterId, $data)) {
            if (null == $value) {
                unset($data[$filterId]);
            } else foreach ($data[$filterId] as $k => $v) {
                if ($v == $value) {
                    unset($data[$filterId][$k]);
                }
            }
        }

        return $helper->url('product.category', [
            'categoryPath'                  => $productFilter->getCategory()->getPath(),
            \View\Product\FilterForm::$name => $data,
        ]);
    };

    $filters = [];
    foreach ($productFilter->getFilterCollection() as $filter) {

        if (!in_array($filter->getId(), $selected)) {
            continue;
        }

        $value = $productFilter->getValue($filter);
        switch ($filter->getTypeId()) {
            case \Model\Product\Filter\Entity::TYPE_SLIDER:
            case \Model\Product\Filter\Entity::TYPE_NUMBER:
                if (empty($value['from']) && empty($value['to'])) continue;
                $name = [];
                $isPrice = $filter->isPrice();

                $links = [];

                if (isset($value['from']) && !($isEqualNumeric($value['from'], $filter->getMin()))) {
                    if ($isPrice) {
                        $links[] = [
                            'name' => sprintf('от %d', $value['from']),
                            'url'  => $getUrl($filter->getId()),
                        ];
                    } else {
                        $links[] = [
                            'name' => 'от ' . round($value['from'], 1),
                            'url'  => $getUrl($filter->getId()),
                        ];
                    }
                }
                if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                    if ($isPrice) {
                        $name[] = sprintf('до %d', $value['to']);
                    } else {
                        $name[] = 'до ' . round($value['to'], 1);
                    }
                }
                if (!$name) continue;
                if ($isPrice) $name[] .= 'р.';
                $filters[] = [
                    'name'  => $filter->getName(),
                    'links' => $links,
                ];
                break;
            case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                if (!is_array($value) || count($value) == 0) continue;
                foreach ($value as $v) {
                    $filters[] = [
                        'name'  => $filter->getName(),
                        'links' => [
                            ['name' => $v == 1 ? 'да' : 'нет', 'url' => $getUrl($filter->getId(), $v)]
                        ],
                    ];
                }
                break;
            case \Model\Product\Filter\Entity::TYPE_LIST:
                if (!is_array($value) || count($value) == 0) continue;
                foreach ($filter->getOption() as $option) {
                    if (in_array($option->getId(), $value)) {
                        $filters[] = [
                            'name'  => $filter->getName(),
                            'links'   => [
                                ['name' => $option->getName(), 'url' => $getUrl($filter->getId(), $option->getId())]
                            ],
                        ];
                    }
                }
                break;
            default:
                continue;
        }
    }

    //var_dump($filters);

    if (!(bool)$filters) {
        //return;
    }
?>

    <!-- Списоки выбранных параметров -->
    <div class="bFilterFoot">
        <ul class="bFilterCheckedParams clearfix">
            <li class="bFilterCheckedParams__eItem mTitle">Цена</li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">от 2 000p</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">до 1 000 000p</span></li>
        </ul>

        <ul class="bFilterCheckedParams clearfix mLast">
            <li class="bFilterCheckedParams__eItem mTitle">Бренд</li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Ahava</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Bubchen</span></li>

            <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Агентство старинных развлечений "Работорцы"</span></li>

            <li class="bFilterCheckedParams__eItem mParams mClearAll"><a class="bDelete" href=""><strong class="bParamsName">Очистить все</strong></a></li> <!-- Добаялется только в списке идущем по очереди последним -->
        </ul>
    </div>
    <!-- /Списоки выбранных параметров -->

<? };