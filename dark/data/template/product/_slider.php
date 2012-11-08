<?php
/**
 * @var $page           \View\Layout
 * @var $productList    \Model\Product\Entity[]
 * @var $product        \Model\Product\Entity
 * @var $title          string
 * @var $itemsInSlider  int
 * @var $totalProducts  int
 * @var $url            string
 * @var $gaEvent        string
 */
?>

<?php $totalPages = (int)ceil($totalProducts / $itemsInSlider) ?>
<?php $gaEvent = isset($gaEvent) ? $gaEvent : null ?>

<div class="carouseltitle carbig">
    <div class="rubrictitle"><h3><?php echo $title ?></h3></div>
    <?php if ($totalPages > 1) { ?>
    <div class="scroll" data-quantity="<?php echo $totalProducts ?>">
        (страница <span>1</span> из <span><?php echo $totalPages ?></span>)
        <a title="Предыдущие <?php echo $itemsInSlider ?>" class="srcoll_link_button back disabled" data-url="<?php echo $url ?>" href="javascript:void(0)"></a>
        <a title="Следующие <?php echo $itemsInSlider ?>" class="srcoll_link_button forvard" data-url="<?php echo $url ?>" href="javascript:void(0)"></a>
    </div>
    <?php } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
    <?php foreach ($productList as $i => $item): ?>
        <?php echo $page->render('product/show/_extra_compact', array('product' => $item, 'isHidden' => $i >= $itemsInSlider, 'gaEvent' => $gaEvent)) ?>
    <?php endforeach ?>
</div>

<div class="clear"></div>