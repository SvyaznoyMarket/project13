<?php
/**
 * @var $productCategory ProductCategoryEntity
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $url string
 * @var $sf_data mixed
 */
?>

<?php if(count($productFilter->getFilterList())): ?>

<!-- Filter -->
<form class="product_filter-block"
      action=""
      method="get"
      data-action-count="<?php echo url_for('productCatalog_count', array('productCategory' => $productCategory->getTokenPrefix() ? ($productCategory->getTokenPrefix().'/'.$productCategory->getToken()) : $productCategory->getToken())) ?>">
	
	<div class="filterresult product_count-block">
		<div class="corner"><div></div></div>
		Выбрано <span class="result">result.data</span> модел<span class="ending">ending</span><br/>
		<a>Показать</a>
	</div>
  
  <dl class="bigfilter form bSpec">
    <dt class="filterHeader">Выбираем:<i></i></dt>
    <?php require '_filter_selected_.php' ?>
    <?php $openNum = 0; ?>
    <?php $i = 0; foreach ($productFilter->getFilterList() as $filter): ?>
    <?php
    if ($filter->getFilterId() == 'price' || $filter->getFilterId() == 'brand') {
      $open = 'block';
    } elseif ($openNum < 5) {
      $openNum++;
      $open = 'block';
    } else {
      $open = 'none';
    }
    ?>
    <?php
    switch ($filter->getTypeId()) {
      case ProductCategoryFilterEntity::TYPE_NUMBER:
      case ProductCategoryFilterEntity::TYPE_SLIDER:
        require '_filter_slider.php';
        break;
      case ProductCategoryFilterEntity::TYPE_LIST:
        require '_filter_list.php';
        break;
      case ProductCategoryFilterEntity::TYPE_BOOLEAN:
        require '_filter_choice.php';
        break;
    }?>
    <?php endforeach; ?>
    <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать"/></div>
  </dl>
</form>

<!-- /Filter -->
<?php endif; ?>