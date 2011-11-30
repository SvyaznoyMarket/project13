<?php foreach ($list as $item): ?>
<div class="pb15"><strong><?php echo $item['name'] ?></strong></div>
	<?php $i = 0; foreach ($item['parameters'] as $parameter): ?>
		<div class="point">

			<?php if (empty($parameter['description']) || true): ?>
			<div class="title"><h3><?php echo $parameter['name'] ?></h3></div>

			<?php else: ?>
			<div class="title"><h3><?php echo $parameter['name'] ?><b></b></h3>
				<div class="pr">
					<div class="prompting">
            <i class="corner"></i>
            <i class="close" title="Закрыть">Закрыть</i>
		        <div class="font16 pb5"><?php echo $parameter['name'] ?></div>
						<?php echo $parameter['description'] ?>
					</div>
				</div>
			</div>

			<?php endif ?>

			<div class="description">
				<?php echo $parameter['value'] ?>
				<!--<div></div>-->
			</div>

		</div>
	<?php endforeach ?>
<?php endforeach ?>
