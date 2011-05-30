<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><a href="<?php echo url_for('productCatalog_category', $item['productCategory']) ?>"><?php echo $item['name'] ?></a></strong>
  </li>
<?php endforeach ?>
</ul>