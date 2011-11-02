<?php if ($task->error): ?>
<ul>
<?php foreach($task->getErrorData() as $error): ?>
  <li><?php echo $error ?></li>
<?php endforeach ?>
</ul>

<?php else: ?>
  <?php echo $task->error ?>

<?php endif ?>
