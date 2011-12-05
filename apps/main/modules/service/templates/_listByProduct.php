<?php if (count($list)): ?>
<div class="line pb15"></div>
<?php
include_component('product', 'f1_lightbox', array('f1' => $list,'product'=>$product, 'servListId' => $servListId))
?>

<div class="bF1Info bBlueButton">
    <?php if (count($servListId)) { ?>
        <h3>Вы добавили услуги:</h3>
          <?php
          foreach ($list as $service) {  ?>  
                <?php if (in_array($service->id, $servListId)) { ?>
                    <div>
                        <?php echo $service->name ?> - <?php echo $service->price ?>&nbsp;<span class="rubl">p</span><br>
                        <a class="bBacketServ__eMore" href="<?php echo url_for('cart_service_delete', array('service' => $service->token, 'product' => $product->token));?>">Отменить услугу</a>
                    </div>        
                <?php } ?>
         <?php } ?>                        
        
    <?php } else { ?>
        <h3>Выбирай услуги F1<br> вместе с этим товаром</h3>        
    <?php } ?>
    <a class="link1" href="">Выбрать услуги</a>
</div>

<?php endif ?>    