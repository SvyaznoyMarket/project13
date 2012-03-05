<?php slot('title','Юридические консультации') ?>
<div class="float100">
  <div class="column685 ">
    <ul class="error_list"><li><?php if (isset($error)) echo $error ?></li></ul>
    <form action="<?php echo url_for('user_legalConsultationSend')?>" class="bForm" method="post">
      <?php foreach ($form as $name => $field): ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
      <?php if (sfContext::getInstance()->getController()->componentExists('callback', 'Field'.$name)): ?>
        <?php include_component('callback', 'Field'.$name, array('form' => $form)) ?>
        <?php else: ?>
        <div class="pb10">
          <?php echo $form[$name]->renderLabel() ?>:
          <?php echo $form[$name]->renderError() ?>
        </div>
        <?php echo $form[$name]->render() ?>
        <?php endif ?>
      <?php endforeach; ?>
      <div class="bComment">Все поля обязательны для заполнения</div>
      <input type="submit" value="Отправить сообщение" id="bigbutton" class="bYellowButton button yellowbutton" />
    </form>
  </div>
</div>

<?php include_component('user', 'menu') ?>
