<?php
/**
 * @var $page       \View\Layout
 * @var $categories \Model\Product\Category\Entity[]
 */
?>

<? foreach ($categories as $category): ?>
    <a id="topmenu-root-<?= $category->getId() ?>" class="bToplink" title="<?= $category->getName() ?>" href="<?= $category->getLink() ?>"></a>
<? endforeach ?>
