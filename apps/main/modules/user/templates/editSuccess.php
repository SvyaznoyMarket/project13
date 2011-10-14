<?php
slot('title','Профиль пользователя');
slot('navigation');
 // include_component('user', 'navigation');
end_slot();
?>
    <div class="float100">
		<div class="column685 ">
            <?php include_component('user', 'profile', array('form' => $form)) ?>            
        </div>
    </div>

<?php include_component('user', 'menu') ?>
