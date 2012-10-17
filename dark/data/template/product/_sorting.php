<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $productSorting \Model\Product\Sorting
 */
?>

<?php
$list = array();

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
<div id="sorting" class="filter" data-sort="<?= implode('-', array($active['name'], $active['direction'])) ?>">
    <span class="fl">Сортировать:</span>
    <div class="filterchoice">
        <a href="<?= $active['url'] ?>" class="filterlink"><?= $active['title'] ?></a>
        <div class="filterlist">
            <a href="<?= $active['url'] ?>" class="filterlink"><?= $active['title'] ?></a>
            <ul>
            <? foreach ($list as $item): ?>
                <li><a href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
            <? endforeach ?>
            </ul>
        </div>
    </div>
</div>
<!-- /Filter -->
