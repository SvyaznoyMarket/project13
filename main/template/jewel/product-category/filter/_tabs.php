<?php

use Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 * @var $formName      string
 */
?>

 
  <div class="bBrandSubNav__eTitle"><?= $category->getName() ?></div>
  <? foreach($filters as $key => $filter) { ?>
    <? if(!empty($catalogJson['sub_category_filter_menu']) && mb_strtolower($catalogJson['sub_category_filter_menu']) == mb_strtolower($filter->getName())) { ?>
      <?php $values = $productFilter->getValue($filter) ?>
      <nav class="bBrandSubNavList">  
        <ul>
          <li class="bBrandSubNavList__eItem"><a class="bBrandSubNavList__eLink<?= empty($values) ? ' active' : '' ?>" href="?scrollTo=<?= $scrollTo ?>">Все</a></li>
          <? foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
            <li class="bBrandSubNavList__eItem"><a class="bBrandSubNavList__eLink<?= in_array($id, $values) ? ' active' : '' ?>" href="?f<?= urlencode('[' . strtolower($filter->getId()) . '][]' ) . '=' . $id ?>&scrollTo=<?= $scrollTo ?>"><?= $option->getName() ?></a></li>
          <? } ?>
        </ul>
      </nav>
    <? } ?>
  <? } ?>

