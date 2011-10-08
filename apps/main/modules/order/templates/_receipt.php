    <!-- Cheque -->
    <div class="cheque">
        <div class="chequetop">
            <div class="chequebottom">
                <div class="top font16">Ваш заказ:</div>
                <ul>
                  <?php foreach ($cart->getProducts() as $product): ?>
                    <li><?php echo $product->name ?> (<?php echo $product->cart['quantity'] ?> шт)<br /><strong><?php echo ($product->price * $product->cart['quantity']) ?> <span class="rubl">&#8399;</span></strong></li>
                  <?php endforeach ?>
                </ul>
                <div class="total">
                    Сумма заказа:<br />
                    <strong class="font14"><?php echo $cart->getTotal() ?> <span class="rubl">&#8399;</span></strong><br />
                    <!--Дата доставки: 5 октября 20011 г.-->
                </div>
            </div>
        </div>
    </div>
    <!-- /Cheque -->
