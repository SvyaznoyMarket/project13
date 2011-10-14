order list
<ul>
<?php foreach ($list as $item): ?>
  <li>
    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a>
    <?php include_component('order', 'show', array('view' => 'compact', 'order' => $item['order'])) ?>
  </li>
<?php endforeach ?>
</ul>