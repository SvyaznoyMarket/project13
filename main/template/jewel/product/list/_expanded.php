<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $isAddInfo              bool
 **/
?>

<? foreach ($pager as $i => $product): ?>
    <?= $page->render('jewel/product/show/_expanded', array('product' => $product, 'addInfo' => $isAddInfo?\Kissmetrics\Manager::getProductSearchEvent($product, $i, $pager->getPage()):[])) ?>
<?php endforeach ?>