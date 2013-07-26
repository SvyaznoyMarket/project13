<?php
/**
 * @var $page           \View\Layout
 * @var $productSorting \Model\Product\Sorting
 */
?>

<?php
$list = [];

$active = $productSorting->getActive();
$active['url'] = $page->helper->replacedUrl(array('sort' => implode('-', array($active['name'], $active['direction']))));
foreach ($productSorting->getAll() as $item)
{
    if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) continue;

    $item['url'] = $page->helper->replacedUrl(array('sort' => implode('-', array($item['name'], $item['direction']))));
    $list[] = $item;
}
?>

<!-- Filter -->
<div id="sorting" class="bSorting" data-sort="<?= implode('-', array($active['name'], $active['direction'])) ?>">
    <span class="bTitle">Сортировать</span>
    <ul class="bSortingList clearfix">
        <li class="bSortingList__eItem"><a class="bSortingList__eItemLink mActiveLink" href="<?= $active['url'] ?>"><?= $active['title'] ?></a></li>
        <? foreach ($list as $item): ?>
            <li class="bSortingList__eItem"><a class="bSortingList__eItemLink" href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
        <? endforeach ?>
    </ul>
</div>
<!-- /Filter -->
