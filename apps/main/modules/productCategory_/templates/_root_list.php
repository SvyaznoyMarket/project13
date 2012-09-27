<?php foreach ($list as $item): /** @var $item ProductCategoryEntity */ ?>
	<a id="topmenu-root-<?php echo $item->getId() ?>" class="bToplink"
	title="<?php echo $item->getName() ?>"
	href="<?php echo $item->getLink() ?>"></a>
<?php endforeach ?>
