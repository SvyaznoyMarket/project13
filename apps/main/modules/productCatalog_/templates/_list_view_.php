<!-- View -->
<div class="view">
  <span>Вид страницы:</span>
  <?php foreach ($list as $item): ?>
  <a href="<?php echo $item['url'] ?>" class="<?php echo $item['class'] . ($item['current'] ? ' active' : '') ?>"
     title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
  <?php endforeach ?>
</div>
<!-- View -->
