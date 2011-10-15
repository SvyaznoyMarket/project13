<?php if (isset($page['name']) && !empty($page['name'])): ?>
  <?php slot('title', $page['name']) ?>
<?php endif ?>

<?php echo sfOutputEscaper::unescape($page['content']) ?>
