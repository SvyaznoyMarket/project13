<?php
/**
 * @var $page \View\ProductCategory\LeafPage
 * @var $category \Model\Product\Category\Entity
 * @var $slice \Model\Slice\Entity
 */

?>

<ul class="bread-crumbs">
    <? if ($slice && $category->getUi()) : ?>
        <li class="bread-crumbs__item"><a href="<?= $page->url('slice.show', ['sliceToken' => $slice->getToken()]) ?>" class="bread-crumbs__link underline"><?= $slice->getName() ?></a></li>
    <? endif ?>
    <? foreach ($category->getAncestor() as $ancestor) : ?>
        <li class="bread-crumbs__item"><a href="<?= $ancestor->getLink() ?>" class="bread-crumbs__link underline"><?= $ancestor->getName() ?></a></li>
    <? endforeach ?>
        <li class="bread-crumbs__item"><?= $category->getName() ?></li>
</ul>

