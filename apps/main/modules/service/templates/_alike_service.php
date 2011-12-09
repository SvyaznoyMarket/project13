<?php if (count($list)) { ?>
<div class="rubrictitle"><h3>А так же есть похожие услуги:</h3></div>
<div class="line pb15"></div>
<div class="clear"></div>

<div class="bServiceCardWrap">
    
<?php $num = 0; ?>    
<?php foreach ($list as $service) { ?>
		<div class="bServiceCard mInlineBlock">
			<div class="bServiceCard__eImage">
                <?php if ($service['photo']) { ?>
                    <div class="bServiceCard__eLogo"></div>
                    <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                        <img src="<?php echo $service['photo']; ?>">
                    </a>    
                <?php } else { ?>
                    <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                        <div class="bServiceCard__eLogo_free"></div>
                    </a>    
                <?php } ?>
            </div>
			<p class="bServiceCard__eDescription">
                <a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" >
                    <?php echo $service['name']; ?>                
                </a>
            </p>
			<div class="bServiceCard__ePrice">
                <?php echo $service['price']; ?>
                <?php if((int)$service['price']) { ?>
                    <span class="rubl">p</span>
                <?php } ?>    
            </div>
            <!--
            <form action="<?php echo url_for('cart_service_add', array('service' => $service['token'])) ?>" />
                <input data-url="<?php echo url_for('cart_service_add', array('service' => $service['token'])) ?>" type="submit" class="button yellowbutton" value="Купить услугу">
            </form>    
            -->            
		</div>
<?php
$num++;
if ($num >= 4) break;
?>    
<?php } ?>    

</div>
<?php } ?>
