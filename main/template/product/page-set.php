<?php
/**
 * @var $page           \View\Product\SetPage
 * @var $pager          \Iterator\EntityPager
 * @var $categoriesById \Model\Product\Category\Entity[]
 */
?>

<?= $page->render('product/_list', array('pager' => $pager, 'view' => 'expanded')) ?>