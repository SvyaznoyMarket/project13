<?php
/**
 * @var $page  \View\Layout
 * @var $pager \Iterator\EntityPager
 */
?>

<div class="pageslist bPagesListTop">
    <div class="bTitle">Страницы</div>
    <ul class="bPagesList">
        <li class="bPagesList__eItem"><a class="bPagesList__eItemLink" href="<?= $page->helper->replacedUrl(array('page' => $pager->getPage())) ?>"><?= $pager->getPage() ?></a></li>
    </ul>
</div>
