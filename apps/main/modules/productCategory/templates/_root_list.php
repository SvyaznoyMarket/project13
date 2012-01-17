<ul class="topmenu">
<?php $i = 0; foreach ($list as $item): $i++; ?>
	<?php if ($i > 9) break; ?>
    <li><a id="topmenu-root-<?php echo $item['root_id'] ?>" href="<?php echo url_for('productCatalog_category', array('productCategory' => $item['token_prefix'] ? ($item['token_prefix'].'/'.$item['token']) : $item['token'])) ?>" class="point<?php echo $i ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>
