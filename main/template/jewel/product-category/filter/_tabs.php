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

<?php $values = $productFilter->getValue($filter) ?>

<ul class="brand-subnav__list">
    <li><a <?= empty($values) ? 'class="active"' : '' ?> href="<?= $category->getLink()?>?scrollTo=<?= $scrollTo ?>">Все</a></li>
    <? foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
        <li><a <?= in_array($id, $values) ? 'class="active"' : '' ?> href="<?= $category->getLink()?>?f<?= urlencode('[' . strtolower($filter->getId()) . '][]' ) . '=' . $id ?>&scrollTo=<?= $scrollTo ?>"><?= $option->getName() ?></a></li>
    <? } ?>
</ul>
