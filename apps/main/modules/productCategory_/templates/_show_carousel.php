<?php
/**
 * @var ProductCategoryTagView $productTagCategory
 * @var int $maxPerPage
 */
$category = $productTagCategory->category;
$productList = $productTagCategory->productList;
$productCount = $productTagCategory->productCount;
$view = $category->getHasLine() ? 'line' : 'compact';
?>
<!-- Carousel -->
<div class="carouseltitle">
  <div class="rubrictitle">
    <h2>
      <a href="<?php echo $category->getLink() ?>" class="underline">
        <?php echo $category->getName()?>
      </a>
    </h2>
  </div>

  <?php if ($productCount > 3): ?>
  <div class="scroll">
    <span><a href='<?php echo $category->getLink() ?>' class='srcoll_link'>посмотреть все</a></span><span
    class="jshm">( <?php echo $productCount?> )</span>
    <a href="javascript:void(0)"
       data-url="<?php echo $productTagCategory->getDataUrl() ?>"
       class="srcoll_link_button back disabled" title="Предыдущие 3"></a>
    <a href="javascript:void(0)"
       data-url="<?php echo $productTagCategory->getDataUrl() ?>"
       class="srcoll_link_button forvard" title="Следующие 3"></a>
  </div>
  <?php endif ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="carousel">
  <?php $i = 0; foreach ($productList as $item): $i++; ?>
  <?php include_partial('productCatalog_/show_', array('view' => $view, 'ii' => $i, 'item' => $item, 'maxPerPage' => $maxPerPage)) ?>
  <?php endforeach ?>
</div>
<!-- Carousel -->