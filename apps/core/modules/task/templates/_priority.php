<?php if (0 == $task->priority): ?>
  <span class="green"><?php echo $task->priority ?></span>

<?php elseif (1 == $task->priority): ?>
  <span class="red"><?php echo $task->priority ?></span>

<?php else: ?>
  <?php echo $task->priority ?>

<?php endif ?>
