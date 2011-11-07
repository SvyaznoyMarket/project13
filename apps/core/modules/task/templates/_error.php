<?php if ($task->error): ?>
<pre style="width: 400px; height: 100px; overflow: scroll">
  <?php echo $task->error ?>
</pre>

<?php else: ?>
  <?php echo $task->error ?>

<?php endif ?>
