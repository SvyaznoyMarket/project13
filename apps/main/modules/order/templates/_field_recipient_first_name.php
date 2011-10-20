        </div>
        <div class="clear width500 pb25"></div>
        <div class="fl width215 mr20"><strong class="font16 deliverytext">Кому и куда доставить:</strong></div>
        <div class="fl width430">
            <div class="pb15">
              <?php echo $form['recipient_first_name']->renderLabel() ?>
              <?php echo $form['recipient_first_name']->renderError() ?>
            </div>

      <?php echo $form['recipient_first_name']->render(array('class' => 'text width418 mb15', )) ?>
