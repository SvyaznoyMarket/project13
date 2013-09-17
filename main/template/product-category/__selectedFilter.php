<?php

return function(
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

    $listById = [];
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
                        'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-from' => null]),
                    ];
                }
                if (isset($value['to']) && !($isEqualNumeric($value['to'], $filter->getMax()))) {
                    $links[] = [
                        'name' => $isPrice ? sprintf('до %sр', $helper->formatPrice($value['to'])) : sprintf('до %s', round($value['to'], 1)),
                        'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-to' => null]),
                    ];
                }

                break;
            case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                if (!is_array($value) || count($value) == 0) continue;
                foreach ($value as $v) {
                    $links[] = [
                        'name' => ($v == 1) ? 'да' : 'нет',
                        'url'  => $helper->replacedUrl(['f- ' . $filter->getId() => null]),
                    ];
                }
                break;
            case \Model\Product\Filter\Entity::TYPE_LIST:
                if (!is_array($value) || count($value) == 0) continue;
                foreach ($filter->getOption() as $option) {
                    if (!in_array($option->getId(), $value)) continue;
                    $links[] = [
                        'name' => $option->getName(),
                        'url'  => $helper->replacedUrl(['f-' . $filter->getId() . '-' . \Util\String::slugify($option->getName()) => null]),
                    ];
                }
                break;
            default:
                continue;
        }

        if (!(bool)$links) continue;

        if (!isset($listById[$filter->getId()])) {
            $listById[$filter->getId()] = ['name' => $filter->getName(), 'links' => []];
        }
        $listById[$filter->getId()]['links'] += $links;
    }

    //var_dump($listById);

    if (!(bool)$listById) {
        return;
    }
?>

    <!-- Списоки выбранных параметров -->
    <div class="bFilterFoot">
        <? $i = 1; $count = count($listById); foreach ($listById as $item): ?>
            <ul class="bFilterCheckedParams clearfix">
                <li class="bFilterCheckedParams__eItem mTitle"><?= $item['name'] ?></li>

                <? foreach ($item['links'] as $link): ?>
                    <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href="<?= $link['url'] ?>"></a><span class="bParamsName"><?= $link['name'] ?></span></li>
                <? endforeach ?>

                <? if ($i === $count): ?>
                    <li class="bFilterCheckedParams__eItem mParams mClearAll"><a class="bDelete" href="<?= $baseUrl ?>"><strong class="bParamsName">Очистить все</strong></a></li>
                <? endif ?>
            </ul>
        <? $i++; endforeach ?>
    </div>
    <!-- /Списоки выбранных параметров -->

<? };