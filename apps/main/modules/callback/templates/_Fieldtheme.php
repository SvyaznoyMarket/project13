<div class="bInputBlock"> 
        <h2 class="bInputBlock__eH2"><?php echo $form['theme']->renderLabelName() ?></h2>
        <p class="bInputBlock__eP">Четко сформулированная тема сообщения облегчит поиск вашего письма среди остальных</p>
        <?php echo $form['theme']->renderError() ?>
        <?php echo $form['theme']->render() ?>
</div>
