<?php slot('title','Профиль пользователя') ?>
<?php slot('navigation') ?>
  <?php include_component('user', 'navigation') ?>
<?php end_slot() ?>
    <div class="float100">
		<div class="column685">
            <?php include_component('user', 'profile', array('form' => $form)) ?>
        </div>
    </div>

<?php include_component('user', 'menu') ?>
