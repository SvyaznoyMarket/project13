<?php if ($first != $page): ?>
  <span><a href="<?php echo pager_url_for($first) ?>">&laquo;</a></span>
<?php else: ?>
  <span>&laquo;</span>
<?php endif ?>

<?php if ($page > $first): ?>
  <span><a href="<?php echo pager_url_for($page - 1) ?>">&lsaquo;</a></span>
<?php else: ?>
  <span>&lsaquo;</span>
<?php endif ?>

<?php foreach($list as $item): ?>
  <?php if ($item == $page): ?>
    <span class="current"><?php echo $item ?></span>

  <?php else: ?>
    <span><a href="<?php echo pager_url_for($item) ?>"><?php echo $item ?></a></span>

  <?php endif ?>
<?php endforeach ?>

<?php if ($page < $last): ?>
  <span><a href="<?php echo pager_url_for($page + 1) ?>">&rsaquo;</a></span>
<?php else: ?>
  <span>&rsaquo;</span>
<?php endif ?>

<?php if ($last != $page): ?>
  <span><a href="<?php echo pager_url_for($last) ?>">&raquo;</a></span>
<?php else: ?>
  <span>&raquo;</span>
<?php endif ?>
