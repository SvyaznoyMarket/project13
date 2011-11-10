<?php if (0 == $task->priority): ?>
  <span class="mark green"><?php echo $task->priority ?></span>

<?php elseif (1 == $task->priority): ?>
  <span class="mark red"><?php echo $task->priority ?></span>

<?php else: ?>
  <span class="mark gray"><?php echo $task->priority ?></span>

<?php endif ?>
