<?php
/**
 * @var $categoryTree \light\ServiceData
 */

?>

<div class="servicebanner"></div>
<div class="slogan">
  <strong>Доставим радость, настроим комфорт!</strong>
  Специалисты F1 привезут и соберут шкаф, повесят телевизор, куда скажете, и установят стиральную машину по всем правилам.
</div>

<?php
$num = 0;
$tmp = array();
foreach($categoryTree->getChildren() as $item){
	/** @var $item ServiceData */
	$tmp[$item->getIconClass()] = $item;
}

$sort = array( 'icon1', 'icon2', 'icon3', 'icon4');

foreach($sort as $key){
    if (!isset($tmp[$key])) continue;
	$item = $tmp[$key];
	?>
	<div class="servicebox fl">
		<div class="serviceboxtop"></div>
		<div class="serviceboxmiddle">
			<a href="<?php echo $item->getLink() ?>">
				<i class="<?php echo $item->getIconClass() ?>"></i>
				<strong class="font16"><?php echo $item->getName() ?></strong>
				<?php echo $item->getDescriptionByIcon(); ?>
			</a>
			<a class="servicebox__choice"href="<?php echo $item->getLink(); ?>">
				выбрать услуги >
			</a>
		</div>
		<div class="serviceboxbottom"></div>
	</div>
<?php $num++; if ($num%2 == 0):?>
    <div class="pb30 clear"></div>
    <?php endif; 
}?>
<div class="pb30 clear"></div>
