<div class="bInputBlock"> 
        <h2 class="bInputBlock__eH2"><?php echo $form['name']->renderLabelName() ?></h2>
        <p class="bInputBlock__eP">Введите имя, чтобы мы знали, как к вам обращаться</p>
        <?php echo $form['name']->renderError() ?>
        <?php echo $form['name']->render() ?>
</div>
