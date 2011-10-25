<!-- Sections -->
<h2>Мебель по разделам</h2>
<div class="line pb15"></div>

<?php foreach ($table as $column): ?>
<ul class="sectionlist">
  <?php foreach ($column as $item): ?>
  <li><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a> (<?php echo $item['product_quantity'] ?>)</li>
  <?php endforeach ?>
</ul>
<?php endforeach ?>

<!-- /Sections -->