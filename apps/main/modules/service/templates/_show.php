<?php 
#JSON data
	$json = array (
		'jsref' => $service['token'],
		'jsimg' => $service['main_photo'],
		'jstitle' => $service['name'],
		'jsprice' => $service['priceFormatted']
	)	
?> 
<div>

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
                <?php if (isset($service['priceFormatted']) && $service['priceFormatted'] && ($showNoPrice || $service['priceFormatted'] != 'бесплатно' )) { ?>
				<strong class="font34">
                    <span class="price"><?php echo $service['priceFormatted']; ?></span>
                    <?php if((int)$service['priceFormatted']) { ?>
                        <span class="rubl">p</span>
                    <?php } ?>    
                </strong>  
                <?php } ?>
                <?php if ((int)$service['price'] >= Service::MIN_BUY_PRICE) { ?>                
                    <a class="link1" href="<?php echo url_for('cart_service_add', array('service' => $service['token'])); ?>">Купить услугу</a>
                <?php } else { ?>  
                    <b>доступна в магазине</b>
                <?php } ?>    
                
			</div>
			
		</div>
</div>

