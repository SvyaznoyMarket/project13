<ul class="topmenu">
<?php $i = 0; foreach ($list as $item): $i++; ?>
	<?php if ($i > 9) break; ?>
    <li><a href="<?php echo url_for('productCatalog_category', $item) ?>" class="point<?php echo $i ?>"><?php echo $item ?></a></li>
<?php endforeach ?>
</ul>
