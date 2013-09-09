<?php
/**
 * @var $page           \View\Layout
 * @var $productSorting \Model\Product\Sorting
 */
?>

<?php
$list = [];

$active = $productSorting->getActive();
$active['url'] = $page->helper->replacedUrl(array('sort' => implode('-', [$active['name'], $active['direction']])));

if ($active['name'] == 'default' && !empty($inSearch)) {
    $active['url'] = $page->helper->replacedUrl(array('sort' => null));
}

foreach ($productSorting->getAll() as $item)
{
    //if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) continue;

    $item['url'] = $page->helper->replacedUrl(array('page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']])));

    if ($item['name'] == 'default' && !empty($inSearch)) {
        $item['url'] = $page->helper->replacedUrl(array('sort' => null));
    }

    $list[] = $item;
}
?>

<!-- Filter -->
<div id="sorting" class="bSorting" data-sort="<?= $active['url'] ?>">
    <span class="bTitle">Сортировать</span>
    <ul class="bSortingList clearfix">
        <? foreach ($list as $item): ?>
            <li class="bSortingList__eItem"><a class="bSortingList__eItemLink<? if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']): ?> mActiveLink<? endif ?>" href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
        <? endforeach ?>
    </ul>
</div>
<!-- /Filter -->
