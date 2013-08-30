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

<div class="pageslist">
    <span class="bTitle">Страницы</span>
    <ul>
        <? if ($current > ($first + 2)): ?>
            <li class="next"><a href="<?= $page->helper->replacedUrl(array('page' => $first)) ?>"><?= $first ?>...</a></li>
        <? endif ?>

        <? foreach (range($first, $last) as $num): ?>
            <? if ($num == $current): ?>
                <li class="current"><a href="#"><?= $num ?></a></li>

            <? elseif ($num >= $current - 2 && $num <= $current + 2): ?>
                <li class="<?= in_array($last, [2,3]) && $last == $num ? 'next' : '' ?>"><a href="<?= $page->helper->replacedUrl(array('page' => $num)) ?>"><?= $num ?></a></li>

            <? endif ?>
        <? endforeach ?>

        <? if ($current < $last - 2): ?>
            <li class="next"><a href="<?= $page->helper->replacedUrl(array('page' => $last)) ?>">
            <? if ($last > 4) { ?>
                ...
            <? } ?>
            <?= $last ?></a></li>
        <? endif ?>
    </ul>
</div>
