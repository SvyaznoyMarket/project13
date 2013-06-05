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

    <li id="sorting" class="last" data-sort="<?= implode('-', array($active['name'], $active['direction'])) ?>">
        <div class="filter-section__title">Сортировать</div>
        <div class="filter-section__value">
            <a href="<?= $active['url'] ?>"><?= $active['title'] ?></a>
            <ul class="filter-section__value__dd">
                <? foreach ($list as $item): ?>
                    <li><a href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
                <? endforeach ?>
            </ul>
        </div>
    </li>

<!-- /Filter -->
