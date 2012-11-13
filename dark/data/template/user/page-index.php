<?php
/**
 * @var $page \View\User\IndexPage
 * @var $menu array
 */
?>

<div class="fl width315">

    <? foreach ($menu as $item): ?>
    <div class="font16 orange pb10"><?= $item['title']?></div>
    <ul class="leftmenu pb20">
        <? foreach ($item['links'] as $link): ?>
        <li>
            <a href="<?= $link['url'] ?>">
                <?= $link['name'] ?>
                <? if (isset($link['num'])) echo '(' . $link['num'] . ')' ?>
            </a>
        </li>
        <? endforeach ?>
    </ul>
    <? endforeach ?>

</div>
