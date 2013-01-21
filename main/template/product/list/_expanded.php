<?php
/**
 * @var $page    \View\Layout
 * @var $pager   \Iterator\EntityPager
 * @var $product \Model\Product\Entity
 * */
?>

<? foreach ($pager as $product): ?>
    <?= $page->render('product/show/_expanded', array('product' => $product)) ?>
<?php endforeach ?>