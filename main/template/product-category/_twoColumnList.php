<?php
/**
 * @var $page        \View\Layout
 * @var $searchQuery string
 * @var $categories  array
 */
?>

<? if(!empty($categories)) {
  $half = ceil(count($categories)/2);
  $categoriesLeft = array_slice($categories, 0, $half);
  $categoriesRight = array_slice($categories, $half, $half);?>

  <div class="twoColumnWrapper mb35">
    <h3><?= count($categories) . ' ' . $page->helper->numberChoice(count($categories), array('категория', 'категории', 'категорий')) ?></h3>
    <div class="fl width327 mr20">
      <ul>
        <? foreach ($categoriesLeft as $category) { ?>
          <li><a href="<?= $category->getLink(); ?>">
            <?= preg_replace("/($searchQuery)/ui", '<span class="bSearchSuggest__eSelected">$1</span>', $category->getName()); ?>
          </a></li>
        <? } ?>
      </ul>
    </div>
    <div class="fl width327">
      <ul>
        <? foreach ($categoriesRight as $category) { ?>
          <li><a href="<?= $category->getLink(); ?>">
            <?= preg_replace("/($searchQuery)/ui", '<span class="bSearchSuggest__eSelected">$1</span>', $category->getName()); ?>
          </a></li>
        <? } ?>
      </ul>
    </div>
    <div class="clear"></div>
  </div>

<? } ?>