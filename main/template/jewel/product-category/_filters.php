<ul class="clearfix">
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
    <? require __DIR__ . '/filter/_dropdown.php' ?>
  <? } ?>

  <? $filtersEmpty = true; 
    foreach ($filters as $filter) {
      $values = $productFilter->getValue($filter);
      if(!empty($values)) $filtersEmpty = false;
    }
  ?>

  <? if(!$filtersEmpty) { ?>
    <li class="reset_filters"><a <?= empty($values) ? 'class="active"' : '' ?> href="<?= $category->getLink()?>?scrollTo=<?= $scrollTo ?>"><div>Показать<br>все</div></a></li>
  <? } ?>

  <? if ($productSorting && $productPager->count()): ?>
    <?= $page->render('jewel/product/_sorting', ['productSorting' => $productSorting, 'scrollTo' => $scrollTo]) ?>
  <? endif ?>
</ul>
 