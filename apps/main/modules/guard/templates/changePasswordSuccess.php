<?php slot('title', 'Смена пароля') ?>

<?php slot('navigation') ?>
  <?php include_component('user', 'navigation') ?>
<?php end_slot() ?>



<div class="float100">
  <div class="column685 ">
    <ul class="error_list"><li><?php if (isset($error)) echo $error ?></li></ul>
    <?php include_partial('guard/form_change_password', array('form' => $form)) ?>
  </div>
</div>

<?php include_component('user', 'menu') ?>
