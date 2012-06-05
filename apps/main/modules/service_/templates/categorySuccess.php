<div class="block">
  <?php foreach ($list as $item): ?>
  <p
    style="padding-left: <?php echo (10 * $item->level) ?>px;"><?php echo $item->level > 1 ? link_to($item->name, 'service_list', $item) : $item['name'] ?></p>
  <?php endforeach; ?>
</div>