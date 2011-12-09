<?php slot('title', 'Обратная связь') ?>

<?php slot('navigation') ?>
  <?php include_component('callback', 'navigation',array()) ?>
<?php end_slot() ?>

<?php slot('left_column', get_component('page', 'menu', array('currentPage' => $currentPage))) ?>



        <ul class="error_list"><li><?php if (isset($error)) echo $error ?></li></ul>
        <form action="<?php echo url_for('callback_send')?>" class="bForm" method="post">    
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
  
<?php slot('seo_counters_advance') ?>
  <?php include_component('callback', 'seo_counters_advance') ?>
<?php end_slot() ?>