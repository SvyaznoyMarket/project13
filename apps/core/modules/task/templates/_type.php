<?php if ('success' == $task->status): ?>
  <span class="mark green"><?php echo $task->type ?></span>

<?php elseif ('fail' == $task->status): ?>
  <span class="mark red"><?php echo $task->type ?></span>

<?php elseif ('pause' == $task->status): ?>
  <span class="mark yellow"><?php echo $task->type ?></span>

<?php else: ?>
  <span class="mark gray"><?php echo $task->type ?></span>

<?php endif ?>
