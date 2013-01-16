<?php
/**
 * @var $page       \View\Layout
 * @var $categories \Model\Product\Service\Category\Entity[]
 * @var $parent     \Model\Product\Service\Category\Entity
 * @var $child      \Model\Product\Service\Category\Entity
 */
?>

<h2>Выбираем услуги</h2>

<div class="line pb10"></div>

<? foreach ($categories as $parent): ?>
    <h2><?= $parent->getName() ?></h2>
    <ul class="leftmenu pb10">
    <? foreach ($parent->getChild() as $child): ?>
        <li>
            <? if ($child->getId() == $category->getId()): ?>
                <strong class="motton"><?= $child->getName() ?></strong>
            <? else: ?>
                <a href="<?= $child->getLink() ?>"><?= $child->getName() ?></a>
            <? endif ?>
        </li>
    <? endforeach ?>
    </ul>
<? endforeach ?>