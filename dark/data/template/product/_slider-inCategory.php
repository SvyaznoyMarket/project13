<?php
/**
 * @var $page          \View\DefaultLayout
 * @var $category      \Model\Product\Category\Entity
 * @var $pager         \Iterator\EntityPager
 * @var $product       \Model\Product\Entity
 * @var $itemsInSlider int
 */

$view = $category->getHasLine() ? 'line' : 'compact';
?>

<!-- Carousel -->
<div class="carouseltitle">
    <div class="rubrictitle">
        <h2>
            <a href="<?= $category->getLink() ?>" class="underline"><?= $category->getName()?></a>
        </h2>
    </div>

    <? if ($pager->count() > 3) { ?>
    <div class="scroll">
        <span><a href='<?= $category->getLink() ?>' class='srcoll_link'>посмотреть все</a></span>
        <span class="jshm">( <?= $pager->count() ?> )</span>
        <a href="javascript:void(0)" data-url="<?= $page->url('product.category.slider', array('categoryPath' => $category->getPath())) ?>" class="srcoll_link_button back disabled" title="Предыдущие 3"></a>
        <a href="javascript:void(0)" data-url="<?= $page->url('product.category.slider', array('categoryPath' => $category->getPath())) ?>" class="srcoll_link_button forvard" title="Следующие 3"></a>
    </div>
    <? } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="carousel">
    <? $i = 0; foreach ($pager as $product) { $i++ ?>
        <?= $page->render('product/show/_' . $view, array('index' => $i, 'product' => $product, 'isHidden' => $i > $itemsInSlider)) ?>
    <? } ?>
</div>
<!-- Carousel -->