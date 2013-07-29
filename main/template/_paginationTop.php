<?php
/**
 * @var $page  \View\Layout
 * @var $pager \Iterator\EntityPager
 */
?>

<?php
$first = 1;
$last = $pager->getLastPage();
$current = $pager->getPage();
?>

<div class="pageslist bPagesListTop">
    <div class="bTitle">Страницы</div>
    <ul class="bPagesList">
        <li class="bPagesList__eItem"><a class="bPagesList__eItemLink" href="<?= $page->helper->replacedUrl(array('page' => $num)) ?>"><?= $num ?></a></li>
    </ul>
</div>
