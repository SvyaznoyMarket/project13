<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) {

    $first = 1;
    $last = $pager->getLastPage();
    $current = $pager->getPage();
?>

    <div class="bSortingLine mPagerBottom clearfix">
        <ul class="bSortingList">
            <li class="bSortingList__eItem mTitle">Страницы</li>
            <? if ($current > ($first + 2)): ?>
                <li class="bSortingList__eItem mSortItem">
                    <a class="bSortingList__eLink" href="<?= $helper->replacedUrl(array('page' => $first)) ?>"><?= $first ?></a>
                </li>
                <li class="bSortingList__eItem mSortItem mDotted">&#8230;</li>
            <? endif ?>

            <? foreach (range($first, $last) as $num): ?>
                <? if ($num == $current): ?>
                    <li class="bSortingList__eItem mSortItem mActive">
                        <span class="bSortingList__eLink" href="#"><?= $num ?></span>
                    </li>

                <? elseif ($num >= $current - 2 && $num <= $current + 2): ?>
                    <? if (in_array($last, [2, 3]) && $last == $num): ?>
                        <li class="bSortingList__eItem mSortItem mDotted">&#8230;</li>
                    <? endif ?>

                    <li class="bSortingList__eItem mSortItem">
                        <a class="bSortingList__eLink" href="<?= $helper->replacedUrl(['page' => $num]) ?>"><?= $num ?></a>
                    </li>

                <? endif ?>
            <? endforeach ?>

            <? if ($current < $last - 2): ?>
                <? if ($last > 4): ?>
                    <li class="bSortingList__eItem mSortItem mDotted">&#8230;</li>
                <? endif ?>
                <li class="bSortingList__eItem mSortItem">
                    <a class="bSortingList__eLink" href="<?= $helper->replacedUrl(['page' => $last]) ?>"><?= $last ?></a>
                </li>
            <? endif ?>

            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mMore" href="">&#8734;</a></li>
        </ul>
    </div>

<? };