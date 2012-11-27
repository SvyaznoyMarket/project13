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
<form>
    <div class="fr width315">
        <div class="font16 orange pb10">Рассылка по электронной почте</div>
         <label class="bSubscibe">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" value="1" autocomplete="off" class="subscibe">
        </label>
        <input type="submit" class="fr button bigbutton" value="Сохранить" tabindex="10"/>
        <div class="clear"></div>
    </div>
</form>