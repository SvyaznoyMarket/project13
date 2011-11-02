<?php if ($task->attempt > 0): ?>
  <span class="red"><?php echo $task->attempt ?></span>

<?php else: ?>
  <?php echo $task->attempt ?>

<?php endif ?>
