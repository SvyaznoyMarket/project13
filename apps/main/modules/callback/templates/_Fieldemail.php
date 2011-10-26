<div class="bInputBlock"> 
        <h2 class="bInputBlock__eH2"><?php echo $form['email']->renderLabelName() ?></h2>
        <p class="bInputBlock__eP">Введите реальный адрес, на него вы получите ответ на ваше сообщение</p>
        <?php echo $form['email']->renderError() ?>
        <?php echo $form['email']->render() ?>
</div>
