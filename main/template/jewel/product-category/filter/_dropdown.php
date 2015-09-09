<?php

/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $scrollTo      string
 */
?>

<?php
$values = $productFilter->getValue($filter);
$options = $filter->getOption();

$allLink = $page->helper->getFilterItemAllLink($productFilter, $filter, $scrollTo);

foreach ($options as $option) {
    if (in_array($option->getId(), $values)) continue;

    $option->setLink($page->helper->getFilterItemOptionLink($allLink, $option, $filter));
}
?>


<li class="bBrandSortingList__eItem js-category-filter-jewel-element" data-name="<?= $page->escape($filter->getName()) ?>">
  <div class="bBrandSortingOptiontTitle"><?= $filter->getName() ?></div>
  <div class="bBrandSortingOption">
    <? if(empty($values)) { ?>
        <a class="bBrandSortingOption__eLink js-category-filter-jewel-element-link" data-title="Все" href="<?= $allLink ?>">Все</a>
    <? } else { ?>
        <? foreach ($options as $optKey => $option) { $id = $option->getId() ?>
            <? if(in_array($id, $values)) { ?>
                <span class="bBrandSortingSelectOption"><?= $option->getName() ?></span>
                <? unset($options[$optKey]) ?>
            <? } ?>
        <? } ?>
    <? } ?>
    <ul class="bBrandSortingOption__eDropDown">
        <? if(!empty($values)) { ?>
            <li class="bDropDownItem"><a class="bDropDownItem__eLink js-category-filter-jewel-element-link" data-title="Все" href="<?= $allLink ?>">Все</a></li>
        <? } ?>
        <? foreach ($options as $option) { ?>
            <li class="bDropDownItem"><a class="bDropDownItem__eLink js-category-filter-jewel-element-link" data-title="<?= $page->escape($option->getName()) ?>" href="<?= $option->getLink() ?>"><?= $option->getName() ?></a></li>
        <? } ?>
    </ul>
  </div>
</li>
