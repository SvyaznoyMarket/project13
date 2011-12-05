<div class="bSet">

        <?php if (isset($service['main_photo'])) { ?>
    
            <div class="bSet__eImage mServiceBig">
                <div class="bServiceCard__eLogo"></div>
                <img src="<?php echo $service['main_photo']; ?>" />
            </div>
            <div class="bSet__eInfo">    
        <?php } else { ?>
        
            <div class="bSet__eImage_small mServiceBig">
                <img alt="" src="/images/f1infobig.png">
            </div>
            <div class="bSet__eInfo_big">            
        <?php } ?>
            

			<p class="bSet__eDescription">
                <?php echo $service['description'] ?>
                 <?php echo $service['work']; ?>
			</p>
			<div class="bSet__ePrice">
                <?php if (isset($service['currentPrice']) && $service['currentPrice']) { ?>
				<strong class="font34">
                    <?php echo $service['currentPrice']; ?>
                    <?php if((int)$service['currentPrice']) { ?>
                        <span class="rubl">p</span>
                    <?php } ?>    
                </strong>  
                <?php } ?>
                <!--
				<a class="link1" href="<?php echo url_for('cart_service_add', array('service' => $service['token'])); ?>">Купить услугу</a>
                -->
			</div>
			
		</div>
</div>

