<ul>
  <?php foreach ($list as $item): ?>
    <li><?php echo $item['name'].' x'.$item['quantity'].' '.link_to('удалить', 'cart_delete', array('product' => $item['token'])) ?></li>
  <?php endforeach ?>
</ul>