<ul class="topmenu">
<?php $i = 0; foreach ($productCategoryList as $productCategory): $i++; ?>
	<?php if ($i > 9) break; ?>
    <li><a href="<?php echo url_for('productCatalog_category', $productCategory) ?>" class="point<?php echo $i ?>"><?php echo $productCategory ?></a></li>
<?php endforeach ?>
</ul>
