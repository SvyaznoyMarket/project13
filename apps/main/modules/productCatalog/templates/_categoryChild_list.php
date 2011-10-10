<!-- Sections -->
<h2><?php echo $productCategory ?> по разделам</h2>
<div class="line pb15"></div>

<?php foreach ($columnList as $column): ?>
<ul class="sectionlist">
  <?php foreach ($column as $i => $item): ?>
  <li>
    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a> (<?php echo $item['product_count'] ?>)
  </li>
  <?php endforeach ?>
</ul>
<?php endforeach ?>

<!-- /Sections -->
