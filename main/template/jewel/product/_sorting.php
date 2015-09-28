<?php
/**
 * @var $page           \View\Layout
 * @var $productSorting \Model\Product\Sorting
 */
?>

<?php
$list = [];

$active = $productSorting->getActive();
$activeUrl = $page->helper->replacedUrl(array('sort' => implode('-', [$active['name'], $active['direction']])));
if(!preg_match('/.*scrollTo=.*/', $activeUrl)) {
    $activeUrl .= preg_match('/.*\?.*/', $activeUrl) ? '&' : '?';
    $activeUrl .= 'scrollTo='.$scrollTo;
}
$active['url'] = $activeUrl;
foreach ($productSorting->getAll() as $item)
{
    if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']) continue;

    $url = $page->helper->replacedUrl(array('page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']])));
    if(!preg_match('/.*scrollTo=.*/', $url)) {
        $url .= preg_match('/.*\?.*/', $url) ? '&' : '?';
        $url .= 'scrollTo='.$scrollTo;
    }
    $item['url'] = $url;
    $list[] = $item;
}
?>

<!-- Filter -->

    <li id="sorting" class="bBrandSortingList__eItem mLast" data-sort="<?= $active['url'] ?>">
        <div class="bBrandSortingTitle">Сортировать</div>
        <div class="bBrandSortingOption">
            <a class="bBrandSortingOption__eLink" href="<?= $active['url'] ?>"><?= $active['title'] ?></a>
            <ul class="bBrandSortingOption__eDropDown">
                <? foreach ($list as $item): ?>
                    <li class="bDropDownItem"><a class="bDropDownItem__eLink js-category-sorting-jewel-element-link" data-sort="<?= $page->escape($item['name'] . '-' . $item['direction']) ?>" href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
                <? endforeach ?>
            </ul>
        </div>
    </li>

<!-- /Filter -->
