<?php if ('success' == $task->status): ?>
  <span class="green"><?php echo $task->type ?></span>

<?php elseif ('fail' == $task->status): ?>
  <span class="red"><?php echo $task->type ?></span>

<?php elseif ('pause' == $task->status): ?>
  <span class="yellow"><?php echo $task->type ?></span>

<?php else: ?>
  <?php echo $task->type ?>

<?php endif ?>
