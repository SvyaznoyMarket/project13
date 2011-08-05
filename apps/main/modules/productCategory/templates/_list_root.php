<ul class="inline">
<?php foreach ($list as $item): ?>
  <li class="block"><a href="<?php echo url_for('productCatalog_category', $item['productCategory']) ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>