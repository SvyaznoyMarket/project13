<?php sfProjectConfiguration::getActive()->loadHelpers('Date'); ?>
<?php slot('title', 'Ваш заказ принят, спасибо за покупку!') ?>
<?php //myDebug::dump($order) ?>
    <!-- Basket -->
        <div class="fl width650 font16 pb20">
            <strong>Номер вашего заказа: <?php echo $order->token ?></strong><br /><br />
            Дата заказа: <?php echo format_date($order->created_at, 'D') ?><br />
            Сумма заказа: <?php include_partial('default/sum', array('sum' => $order->sum, )) ?> <span class="rubl">p</span><br /><br />
            <!--С вами свяжется оператор для получения и уточнения параметров заказа.-->
            <?php if (isset($result)): ?>
            <strong><?php echo $result['stage']['name'] ?>:</strong> <?php echo $result['message'] ?><br />
            <?php endif ?>
            С вами свяжется оператор для получения и уточнения параметров заказа.
        </div>
        <!--div class="fr width250 pb20 form"><label for="radio-1">Я хочу получать СМС уведомления об изменении статуса заказа</label><input id="radio-1" name="radio-1" type="radio" value="radio-1" /></div-->
        <div class="line pb20"></div>
        <!--input type="button" class="button bigbutton fl" value="Посмотреть мои заказы" /-->
        <form method="get" action="<?php echo url_for('homepage') ?>">
          <input type="submit" class="button bigbutton fr" value="Продолжить покупки" />
        </form>
    <!-- /Basket -->
