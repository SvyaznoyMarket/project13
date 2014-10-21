<ul class="bBrandSortingList clearfix">
  <? foreach($filters as $key => $filter) { ?>
    <? // не выводим фильтры, запрещенные в json и указанный в качестве табов
      if((!empty($catalogJson['sub_category_filters_exclude']) && is_array($catalogJson['sub_category_filters_exclude']) &&
          in_array(mb_strtolower($filter->getName()), array_map(function($filterName){
            return mb_strtolower($filterName);
          }, $catalogJson['sub_category_filters_exclude']))) ||
          (!empty($catalogJson['sub_category_filter_menu']) && mb_strtolower($catalogJson['sub_category_filter_menu']) == mb_strtolower($filter->getName()))) {
        continue;
      } 
    ?>
    <?= $page->render('jewel/product-category/filter/_dropdown', [
        'category' => $category,
        'productFilter' => $productFilter,
        'filter' => $filter,
        'scrollTo' => $scrollTo,
      ]) ?>
  <? } ?>

  <? $filtersEmpty = true; 
    foreach ($filters as $filter) {
      $values = $productFilter->getValue($filter);
      if(!empty($values)) $filtersEmpty = false;
    }
  ?>

  <? if(!$filtersEmpty) { ?>
    <li class="bBrandSortingList__eItem mReset"><a <?= empty($values) ? 'class="active"' : '' ?> href="?scrollTo=<?= $scrollTo ?>">Показать<br/>все</a></li>
  <? } ?>

  <? if ($productSorting && $productPager->count()): ?>
    <?= $page->render('jewel/product/_sorting', ['productSorting' => $productSorting, 'scrollTo' => $scrollTo]) ?>
  <? endif ?>
</ul>
 