<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $pager                 \Iterator\EntityPager
 */
$helper = \App::helper();
$first = 1;
$last = $pager->getLastPage();
$current = $pager->getPage();
$diff = 2;

// допустим мы на третьей странице из 10
$range = range($current - $diff, $current + $diff); // формируем массив от текущей страницы => [1,2,3,4,5]
$range = array_filter($range, function($item) use ($first, $last) { return $item > $first && $item < $last; }); // отбрасываем лишние элементы => [2,3,4,5]
if (reset($range) > $first + 1) array_unshift($range, 0); // если текущая страница больше 2, то ставим ... в начале => [2,3,4,5]
if (end($range) < $last - 1) $range[] = 0; // если текущая страница меньше 9, то ставим ... в конце => [2,3,4,5,0]
if (count($range) > 1) array_unshift($range, 1); $range[] = $last; // добиваем первой и последней страницей => [1,2,3,4,5,0,10]

?>

<ul class="sorting_lst fl-r js-category-pagination">
    <li class="sorting_i sorting_i-tl">Страницы</li>

    <? foreach ($range as $i) : ?>
    <? if ($i != 0) : ?>
    <li class="sorting_i <? if ($i == $current) : ?>act js-category-pagination-activePage<? endif ?> js-category-pagination-page">
        <a class="sorting_lk sorting_lk-page jsPagination" href="<?= $helper->replacedUrl(['page' => $i, 'ajax' => null]) ?>"><?= $i ?></a>
    </li>
    <? else : ?>
        <li class="sorting_i <? if ($i == $current) : ?>act js-category-pagination-activePage<? endif ?> js-category-pagination-page">&#8230;</li>
    <? endif ?>
    <? endforeach ?>

    <li class="sorting_i hidden js-category-pagination-paging"><a class="sorting_lk sorting_lk-page jsPaginationEnable" href="#">123</a></li>
    <li class="sorting_i js-category-pagination-infinity"><a class="sorting_lk sorting_lk-page jsInfinityEnable" href="#">∞</a></li>
</ul>
