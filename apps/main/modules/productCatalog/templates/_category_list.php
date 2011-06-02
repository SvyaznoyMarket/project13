<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><a href="<?php echo url_for('productCatalog_category', $item['productCategory']) ?>"><?php echo $item['name'] ?></a></strong><br />
    <?php include_component('productCatalog', 'creator_list', array('productCategory' => $item['productCategory'])) ?>
  </li>
<?php endforeach ?>
</ul>