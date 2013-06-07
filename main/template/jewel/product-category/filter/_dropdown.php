<?php

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

<?php
$values = $productFilter->getValue($filter);
$options = $filter->getOption();

$allLink = $page->helper->getFilterItemAllLink($category, $productFilter, $filter, $scrollTo);

foreach ($options as $option) {
    if (in_array($option->getId(), $values)) continue;

    $option->setLink($page->helper->getFilterItemOptionLink($allLink, $option, $filter));
}
?>


<li>
  <div class="filter-section__title"><?= $filter->getName() ?></div>
  <div class="filter-section__value">
    <? if(empty($values)) { ?>
        <a href="<?= $allLink ?>">Все</a>
    <? } else { ?>
        <? foreach ($options as $optKey => $option) { $id = $option->getId() ?>
            <? if(in_array($id, $values)) { ?>
                <a href=""><?= $option->getName() ?></a>
                <? unset($options[$optKey]) ?>
            <? } ?>
        <? } ?>
    <? } ?>
    <ul class="filter-section__value__dd">
        <? if(!empty($values)) { ?>
            <li><a href="<?= $allLink ?>">Все</a></li>
        <? } ?>
        <? foreach ($options as $option) { ?>
            <li><a href="<?= $option->getLink() ?>"><?= $option->getName() ?></a></li>
        <? } ?>
    </ul>
  </div>
</li>
