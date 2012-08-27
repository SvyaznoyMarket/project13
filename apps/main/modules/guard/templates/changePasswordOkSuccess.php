<?php
slot('title','Смена пароля');
slot('navigation');
  include_component('user', 'navigation');
end_slot();
?>



<div class="float100">
    <div class="column685 green">
        Спасибо, Ваш пароль был изменён.
    </div>
</div>

<?php include_component('user', 'menu') ?>
