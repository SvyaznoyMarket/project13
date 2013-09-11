<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool
 **/
?>

<? foreach ($pager as $i => $product): ?>
    <?= $page->render('product/show/_expanded', array('product' => $product, 'pager' => $pager, 'productPosition' => $i,  'productVideos' => isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [], 'addInfo' => $isAddInfo?\Kissmetrics\Manager::getProductSearchEvent($product, $i, $pager->getPage()):[])) ?>
<?php endforeach ?>