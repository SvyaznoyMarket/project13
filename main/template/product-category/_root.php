<?php
/**
 * @var $page       \View\Layout
 * @var $categories \Model\Product\Category\Entity[]|\Model\Menu\Entity[]
 */
?>

<? foreach ($categories as $category): ?>
    <a id="topmenu-root-<?= $category->getId() ?>" class="bToplink<?= (923 == $category->getId() && time() > strtotime('2013-01-25 00:00:00')) ? ' jew25' : '' ?>" title="<?= $category->getName() ?>" href="<?= $category->getLink() ?>"></a>
<? endforeach ?>
