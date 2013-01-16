<?php
/** @var $page              \View\Layout */
/** @var $categories        \Model\Product\Category\Entity[] */
/** @var $child             \Model\Product\Category\Entity */
/** @var $grandchild        \Model\Product\Category\Entity */
/** @var $columnsByCategory array */
?>

<? foreach($categories as $category): ?>
<div class="extramenu" style="display: none;" id="extramenu-root-<?= $category->getId() ?>">

    <div class="corner">
        <div></div>
    </div>

    <span class="close" href="#"></span>
    <?php for ($column = 0; $column < 4; $column++): ?>
        <?
            $children = array();
            foreach ($category->getChild() as $child) {
                if ($column != $columnsByCategory[$child->getId()]) continue;
                $children[] = $child;
            }
        ?>
        <dl>
            <?php foreach ($children as $child): ?>
                <dt><a href="<?= $child->getLink() ?>"><?= $child->getName() ?></a></dt>
                <?php foreach ($child->getChild() as $grandchild): ?>
                    <dd><a href="<?= $grandchild->getLink() ?>"><?= $grandchild->getName() ?></a></dd>
                <? endforeach ?>
            <? endforeach ?>
        </dl>
    <?php endfor ?>

    <div class="clear"></div>
</div>
<? endforeach ?>