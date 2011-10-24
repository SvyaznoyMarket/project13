        </div>
        <div class="fl width500 pb25"></div>
        <div class="fl width215 mr20"><strong class="font16">Хотите что-то добавить?</strong></div>
        <div class="fl width430">
            <div class="pb10">
              <?php echo $form['extra']->renderLabel() ?>
              <?php echo $form['extra']->renderError() ?>
            </div>

            <div class="textareabox mb5 textdisabled">
              <?php echo $form['extra']->render() ?>
            </div>
            <div class="font11">Укажите любую информацию, которая как Вам кажется поможет нам еще быстрее выполнить заказ.</div>
