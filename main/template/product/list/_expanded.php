<?php
/**
 * @var $page                   \View\Layout
 * @var $pager                  \Iterator\EntityPager
 * @var $product                \Model\Product\Entity
 * @var $isAddInfo              bool
 **/
?>

<? foreach ($pager as $i => $product): ?>
    <?= $page->render('product/show/_expanded', [
        'product'       => $product,
        'addInfo'       => \Kissmetrics\Manager::getProductSearchEvent($product, $i+1, $pager->getPage()),
    ]); ?>
<?php endforeach ?>