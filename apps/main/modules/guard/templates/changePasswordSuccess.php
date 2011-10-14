<?php
slot('title','Пароль');
?>



<div class="float100">
    <div class="column685 ">
        <?php include_partial('guard/form_change_password', array('form' => $form)) ?>
    </div>
</div>

<?php include_component('user', 'menu') ?>
