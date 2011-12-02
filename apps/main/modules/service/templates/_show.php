<div class="bSet">
		<div class="bSet__eImage mServiceBig">
            <?php if (isset($service['main_photo'])) { ?>
                <img src="<?php echo $service['main_photo']; ?>" />
            <?php } ?>
		</div>
		<div class="bSet__eInfo">
			<p class="bSet__eDescription">
                <?php echo $service['description'] ?>
                 <?php echo $service['work']; ?>
			</p>
			<div class="bSet__ePrice">
                <?php if (isset($service['currentPrice']) && $service['currentPrice']) { ?>
				<strong class="font34"><?php echo $service['currentPrice']; ?> <span class="rubl">p</span></strong>  
                <?php } ?>
                <!--
				<a class="link1" href="<?php echo url_for('cart_service_add', array('service' => $service['token'])); ?>">Купить услугу</a>
                -->
			</div>
			
		</div>
</div>

