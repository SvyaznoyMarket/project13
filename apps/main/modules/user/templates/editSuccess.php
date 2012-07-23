<?php slot('title','Профиль пользователя') ?>
<?php slot('navigation') ?>
  <?php include_component('user', 'navigation') ?>
<?php end_slot() ?>

<div class="float100">
<div class="column685">
  <ul class="error_list"><li><?php if (isset($error)) echo $error ?></li></ul>

  <?php include_component('user', 'profile', array('form' => $form)) ?>
</div>
</div>

<?php include_component('user', 'menu') ?>

<br />