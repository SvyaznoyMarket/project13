<?php slot('title', 'Теги') ?>

<?php slot('navigation') ?>
  <?php include_component('tag', 'navigation') ?>
<?php end_slot() ?>

<?php echo include_component('tag', 'list', array('tagList' => $tagList)) ?>
