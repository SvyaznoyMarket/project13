<?php if (!empty($page['header'])): ?>
  <?php slot('title', $page['header']) ?>
<?php endif ?>

<?php if ($page->has_menu) slot('left_column', get_component('page', 'menu', array('page' => $page))) ?>

<?php echo $page['content'] ?>
