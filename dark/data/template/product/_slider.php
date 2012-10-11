<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $productList    \Model\Product\Entity[]
 * @var $product        \Model\Product\Entity
 * @var $title          string
 * @var $perPage        int
 * @var $totalProducts  int
 *
 */
?>

<?php $totalPages = (int)ceil($totalProducts / $perPage) ?>

<div class="carouseltitle carbig">
    <div class="rubrictitle"><h3><?php echo $title ?></h3></div>
    <?php if ($totalPages > 1) { ?>
    <div class="scroll" data-quantity="<?php echo $totalProducts ?>">
        (страница <span>1</span> из <span><?php echo $totalPages ?></span>)
        <a title="Предыдущие 5" class="srcoll_link_button back disabled" data-url="<?php echo $page->url('product.accessories', array('productToken' => $product->getToken())) ?>" href="javascript:void(0)"></a>
        <a title="Следующие 5" class="srcoll_link_button forvard" data-url="<?php echo $page->url('product.accessories', array('productToken' => $product->getToken())) ?>" href="javascript:void(0)"></a>
    </div>
    <?php } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
    <?php foreach ($productList as $i => $item): ?>
        <?php echo $page->render('product/show/_extra_compact', array('product' => $item, 'isHidden' => $i >= $perPage)) ?>
    <?php endforeach ?>
</div>

<div class="clear"></div>