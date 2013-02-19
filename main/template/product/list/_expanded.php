<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $productVideosByProduct array
 **/
?>

<? foreach ($pager as $product): ?>
    <?= $page->render('product/show/_expanded', array('product' => $product, 'productVideos' => isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [])) ?>
<?php endforeach ?>