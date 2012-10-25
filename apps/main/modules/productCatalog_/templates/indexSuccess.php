<?php
/**
 * @var $categoryList ProductCategoryEntity[]
 */
?>
<?php //slot('title', 'Каталог товаров') ?>

<?php //slot('navigation') ?>
<?php //include_component('productCatalog_', 'navigation') ?>
<?php //end_slot() ?>

<?php 
$renderList = function($list) use(&$renderList){
	if(count($list)){
		$level = $list[0]->getLevel();
	?>
	<ul>
	  <?php foreach ($list as $i => $item): ?>
	  	<li class="indexCatalog__<?php echo $level ?>">
	    	<a class="indexCatalog__<?php echo $level ?>" href="<?php echo $item->getLink() ?>"><?php echo $item->getName() ?></a>
	    	<?php $renderList($item->getChildren()); ?>
	    </li>
	  <?php endforeach ?>
	</ul>
	<?	
	}
}
?>
<div class="clear"></div>
<div class="indexCatalog">
	<div class="indexCatalog__slideMenu">
		<h2>Поиск по категориям:</h2>
			<input id="catFind" type="text" class="bBuyingLine__eText"  />
			<input id="catFindButton" class="searchbutton" type="button" />
		<h2>Быстрый переход:</h2>
		<ul>
			<li>
				<a href="#">Рутовая категория №1</a>
			</li>
			<li>
				<a href="#">Рутовая категория №2</a>
			</li>
			<li>
				<a href="#">Рутовая категория №3</a>
			</li>
		</ul>
	</div>
	<?php $renderList($categoryList) ?>
</div>
<? /*
<div>
	<ul>
	  <?php foreach ($list as $i => $item): ?>
	  <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
	    <?php if (0 == $item['level']): ?>
	    <strong><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></strong>
	    <?php else: ?>
	    <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a>
	    <?php endif ?>
	
	    <?php if (isset($list[$i + 1]) && ($list[$i + 1]['level'] < $item['level']) && ($item['level'] < 3)): ?><br/>
	    <br/><?php endif ?>
	  </li>
	  <?php endforeach ?>
	</ul>
</div>
 */
?>
<?php slot('seo_counters_advance') ?>
<?php //include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
