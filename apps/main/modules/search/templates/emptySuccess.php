<?php slot('title', mb_strtoupper('Результаты поиска')) ?>

<?php slot('navigation') ?>
  <?php include_component('search', 'navigation', array('searchString' => $searchString, ),true) ?>
<?php end_slot() ?>

<?php slot('page_head') ?>
  <?php include_partial('search/page_head') ?>
<?php end_slot() ?>

<?php //slot('left_column') ?>
  <?php //include_component('search', 'categories') ?>
<?php //end_slot() ?>

Поиск не дал результатов.