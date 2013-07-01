<?php
/**
 * @var $page                   \View\Layout
 * @var $request                \Http\Request
 * @var $category               \Model\Product\Category\Entity
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $itemsInSlider          int
 * @var $productVideosByProduct array
 */
?>

<?
$view = $category->getHasLine() ? 'line' : 'compact';

$categoryLink = $category->getLink();
// если запрос содержит фильтры
if ($filterData = $request->get(\View\Product\FilterForm::$name)) {
    if (is_array($filterData) && !empty($filterData)) {
        $categoryLink .= '?' . http_build_query($request->query->all());
    }
}

if (\App::request()->get('instore')) {
    $categoryLink .= (false === strpos($categoryLink, '?') ? '?' : '&') . 'instore=1';
}
if (\App::request()->get('shop')) {
    $categoryLink .= (false === strpos($categoryLink, '?') ? '?' : '&') . 'shop='.\App::request()->get('shop');
}
?>

<!-- Carousel -->
<div class="carouseltitle">
    <div class="rubrictitle">
        <h2>
            <a href="<?= $categoryLink ?>" class="underline"><?= $category->getName()?></a>
        </h2>
    </div>

    <? if ($pager->count() > 3) { ?>
    <div class="scroll">
        <span><a href='<?= $categoryLink ?>' class='srcoll_link'>посмотреть все</a></span>
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
        <?= $page->render('product/show/_' . $view, array(
            'index'         => $i,
            'product'       => $product,
            'isHidden'      => $i > $itemsInSlider,
            'productVideos' => isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [],
        )) ?>
    <? } ?>
</div>
<!-- Carousel -->