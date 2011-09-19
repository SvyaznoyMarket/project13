<style type="text/css">
.message {
  padding: 2px 4px;
  background: #91be3f;
  color: #ffffff;
}
.message.error {
  background: #cd0a0a;
}
</style>

<?php if ($sf_user->hasFlash('error')): ?>
  <p class="message error"><?php echo $sf_user->getFlash('error') ?></p>
<?php elseif ($sf_user->hasFlash('message')): ?>
  <p class="message"><?php echo $sf_user->getFlash('message') ?></p>
<?php endif ?>


<?php echo link_to('Перегрузить данные', 'default_init', array(), array('confirm' => 'Выполнить перезагрузку данных?')) ?>