<?php if (count($list)) { ?>
<div class="rubrictitle"><h3>А так же есть похожие услуги:</h3></div>
<div class="line pb15"></div>
<div class="clear"></div>

<div class="bServiceCardWrap">
    
<?php $num = 0; ?>    
<?php foreach ($list as $service) { ?>
		<div class="bServiceCard mInlineBlock" ref="<?php echo $service['token'] ?>">
			<div class="bServiceCard__eImage">
                <?php if ($service['photo']) { ?>
                    <div class="bServiceCard__eLogo"></div>
                    <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                        <img src="<?php echo $service['photo']; ?>">
                    </a>    
                <?php } else { ?>
                    <div class="bServiceCard__eLogo_free"></div>
                    <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                    </a>    
                <?php } ?>
            </div>
			<p class="bServiceCard__eDescription">
                <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                    <?php echo $service['name']; ?>                
                </a>
            </p>
			<div class="bServiceCard__ePrice">
                <span class="price"><?php echo $service['priceFormatted']; ?></span>
                <?php if((int)$service['priceFormatted']) { ?>
                    <span class="rubl">p</span>
                <?php } ?>    
            </div>
            <?php if ((int)$service['price'] >= Service::MIN_BUY_PRICE) { ?>
                <form action="<?php echo url_for('cart_service_add', array('service' => $service['token'])) ?>" />
                    <input data-url="<?php echo url_for('cart_service_add', array('service' => $service['token'])) ?>" type="submit" class="button yellowbutton" value="Купить услугу">
                </form>    
            <?php } ?>
                        
		</div>
<?php
$num++;
if ($num >= 4) break;
?>    
<?php } ?>    

</div>
<?php } ?>
