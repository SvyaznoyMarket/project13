<?php
/**
 * @var $productCategory ProductCategory
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $url string
 * @var $sf_data mixed
 */
?>

<!-- Filter -->
<form class="product_filter-block"
      action=""
      method="get"
      data-action-count="<?php echo url_for('productCatalog__count', $sf_data->getRaw('productCategory')) ?>">

  <dl class="bigfilter form bSpec">
    <h2>Выбираем:</h2>
    <?php include_partial('filter_selected_', $sf_data) ?>
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
    $data = array(
      'productFilter' => $productFilter,
      'filter' => $filter,
      'i' => $i,
      'open' => $open,
    );
    switch ($filter->getTypeId()) {
      case ProductCategoryFilterEntity::TYPE_NUMBER:
      case ProductCategoryFilterEntity::TYPE_SLIDER:
        include_partial('filter_slider', $data);
        break;
      case ProductCategoryFilterEntity::TYPE_LIST:
        include_partial('filter_list', $data);
        break;
      case ProductCategoryFilterEntity::TYPE_BOOLEAN:
        include_partial('filter_choice', $data);
        break;
    }?>
    <?php endforeach; ?>
    <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать"/></div>
  </dl>
</form>

<!-- /Filter -->