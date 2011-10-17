<?php foreach ($product['ParameterGroup'] as $item): ?>
	<div class="pb15"><strong><?php echo $item->getName() ?></strong></div>
	<?php $i = 0; foreach ($item->getParameter() as $property): ?>
		<div class="point">
			<?php $desc = $property->getProperty()->getDescription() ?>
			<?php if (empty($desc)): ?>
			<div class="title"><h3><?php echo $property->getName() ?></h3></div>
			<?php else: ?>
			<div class="title"><h3><?php echo $property->getName() ?><b></b></h3>
				<div class="pr">
					<div class="prompting"><i class="corner"></i><i class="close" title="Закрыть">Закрыть</i>
		                <div class="font16 pb5"><?php echo $property->getName() ?></div>
						<?php echo $property->getProperty()->getDescription() ?>
					</div>
				</div>
			</div>
			<?php endif ?>
			<div class="description">
				<?php echo $property->getValue() ?>
				<!--			<div></div>-->
			</div>
		</div>
	<?php endforeach ?>
<?php endforeach ?>
<script type="text/javascript">
$('.point .title b').click(function(){
	$(this).parent().parent().find('.prompting').show();
});
$('.point .title .pr .close').click(function(){
	$(this).parent().hide();
});
</script>