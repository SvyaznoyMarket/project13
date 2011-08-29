<div class="block">
<?php foreach($list as $item): ?>
  <p><?php echo link_to($item->name, 'service_show', $item) ?></p>
<?php endforeach; ?>
</div>
