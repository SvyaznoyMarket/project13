<?php

use \Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\DefaultLayout
 * @var $productFilter \Model\Product\Filter
 */
?>

<?php
/**
 * @param  int  $filterId
 * @param  null $value
 * @return string
 */
$getUrl = function($filterId, $value = null) use ($page, $productFilter) {
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

    return $page->url('product.category', array(
        'categoryPath'                  => $productFilter->getCategory()->getPath(),
        \View\Product\FilterForm::$name => $data
    ));
};

/**
 * @param $first
 * @param $second
 * @return bool
 */
$isEqualNumeric = function($first, $second) use ($page) {
    $first = $page->helper->clearZeroValue((float)$first);
    $second = $page->helper->clearZeroValue((float)$second);

    return $first == $second;
};

$list = array();
foreach ($productFilter->getFilterCollection() as $filter) {
    $value = $productFilter->getValue($filter);
    switch ($filter->getTypeId()) {
        case FilterEntity::TYPE_SLIDER:
        case FilterEntity::TYPE_NUMBER:
            if (empty($value['from']) && empty($value['to'])) continue;
            $name = array();
            if (!($isEqualNumeric($value['from'], $filter->getMin()))) $name[] = sprintf('от %d', $value['from']);
            if (!($isEqualNumeric($value['to'], $filter->getMax()))) $name[] = sprintf('до %d', $value['to']);
            if (!$name) continue;
            if ($filter->getFilterId() == 'price') $name[] .= 'р.';
            $list[] = array(
                'type' => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
                'name'  => implode(' ', $name),
                'url'   => $getUrl($filter->getFilterId()),
                'title' => $filter->getName(),
            );
            break;
        case FilterEntity::TYPE_BOOLEAN:
            if (!is_array($value) || count($value) == 0) continue;
            foreach ($value as $v) {
                $list[] = array(
                    'type'  => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
                    'name'  => $filter->getName() . ': ' . ($v == 1 ? 'да' : 'нет'),
                    'url'   => $getUrl($filter->getFilterId(), $v),
                    'title' => $filter->getName(),
                );
            }
            break;
        case FilterEntity::TYPE_LIST:
            if (!is_array($value) || count($value) == 0) continue;
            foreach ($filter->getOption() as $option) {
                if (in_array($option->getId(), $value)) {
                    $list[] = array(
                        'type'  => $filter->getFilterId() == 'brand' ? 'creator' : 'parameter',
                        'name'  => $option->getName(),
                        'url'   => $getUrl($filter->getFilterId(), $option->getId()),
                        'title' => $filter->getName(),
                    );
                }
            }
            break;
        default:
            continue;
    }
}
?>

<? if ((bool)$list): ?>
    <dd class="bSpecSel">
        <h3>Ваш выбор:</h3>
        <ul>
            <? foreach ($list as $item): ?>
            <li>
                <a href="<?= $item['url'] ?>" title="<?= $item['title'] ?>"><b>x</b> <?= $item['name'] ?><?= ('price' == $item['type'] ? '&nbsp;<span class="rubl">p</span>' : '') ?></a>
            </li>
            <? endforeach ?>
        </ul>
        <a class="bSpecSel__eReset" href="<?= $page->url('product.category', array('categoryPath' => $productFilter->getCategory()->getPath())) ?>">сбросить все</a>
    </dd>
<? endif ?>