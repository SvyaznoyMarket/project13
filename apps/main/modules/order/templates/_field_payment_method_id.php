        </div>
        <div class="clear width500 pb25"></div>
        <div class="fl width215 mr20"><strong class="font16">Способ оплаты:</strong></div>
        <div class="fl width430">
            <div class="pb15">
              <?php echo $form['payment_method_id']->renderLabel() ?>
              <?php echo $form['payment_method_id']->renderError() ?>
            </div>

      <?php echo $form['payment_method_id']->render() ?>
