    <!-- Cheque -->
    <div class="cheque">
        <div class="chequetop">
            <div class="chequebottom">
                <div class="top font16">Ваш заказ:</div>
                <ul>
                    <?php foreach ($cart->getProductServiceList() as $product): ?>
                        <li><div><?php echo $product['name'] ?> (<?php echo $product['quantity'] ?> шт)</div><strong><?php echo ($product['priceFormatted']) ?> <span class="rubl">p</span></strong></li>

                        <?php foreach($product['service'] as $service){ ?>                    
                            <li><div><?php echo $service['name'] ?> (<?php echo $service['quantity'] ?> шт)</div><strong><?php echo ($service['priceFormatted']) ?> <span class="rubl">p</span></strong></li>
                        <?php } ?>

                    <?php endforeach ?>
                </ul>
                <div class="total">
                    Сумма заказа:<br />
                    <strong class="font14"><?php echo $cart->getTotal(true) ?> <span class="rubl">p</span></strong><br />
                    <!--Дата доставки: 5 октября 20011 г.-->
                </div>
            </div>
        </div>
    </div>
    <!-- /Cheque -->
