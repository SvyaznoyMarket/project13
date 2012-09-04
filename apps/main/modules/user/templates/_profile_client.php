<style type="text/css">
  .error_list {
    color: red;
  }
</style>
<form action="<?php echo url_for('user_update')?>" class="form" method="post">
  <div class="fl width430">

    <?php //echo $form ?>

    <?php foreach ($form as $name => $field): ?>
    <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
    <?php if (sfContext::getInstance()->getController()->componentExists('user', 'Field' . $name)): ?>
      <?php include_component('user', 'Field' . $name, array('form' => $form)) ?>
      <?php else: ?>
      <div class="pb10">
        <?php echo $form[$name]->renderLabel() ?>:
        <?php echo $form[$name]->renderError() ?>
      </div>
      <?php echo $form[$name]->render() ?>
      <?php endif ?>
    <?php endforeach; ?>




    <div class="clear pb20"></div>
    <input type="submit" class="button yellowbutton" id="bigbutton" value="Сохранить изменения"/>
  </div>
</form>


