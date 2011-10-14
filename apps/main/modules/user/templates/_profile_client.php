    <form action="<?=url_for('user_update')?>" class="form" method="post">
        
        <?php //echo $form ?>
        
    <?php foreach ($form as $name => $field): ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
      <?php if (sfContext::getInstance()->getController()->componentExists('user', 'Field'.$name)): ?>
        <?php include_component('user', 'Field'.$name, array('form' => $form)) ?>
      <?php else: ?>
        <?php echo $form[$name]->renderRow(); //myDebug::dump($name); ?>
      <?php endif ?>
   <?php endforeach; ?>     
     
        
        
        
        <div class="clear pb20"></div>
        <input type="submit" class="button yellowbutton" id="bigbutton" value="Сохранить изменения" />
                
    </form>


        <?php //include_component('user', 'Fieldgender') ?>
