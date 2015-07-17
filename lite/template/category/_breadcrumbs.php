<?php
/**
 * @var $page \View\ProductCategory\LeafPage
 * @var $category \Model\Product\Category\Entity
 */

?>

<ul class="bread-crumbs">
    <? foreach ($category->getAncestor() as $ancestor) : ?>
    <li class="bread-crumbs__item"><a href="<?= $ancestor->getLink() ?>" class="bread-crumbs__link underline"><?= $ancestor->getName() ?></a></li>
    <? endforeach ?>
    <li class="bread-crumbs__item"><?= $category->getName() ?></li>
</ul>

