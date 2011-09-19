<?php slot('form_signin', ' ') ?>

<h1>Регистрация</h1>

<div class="block medium">
  <?php echo get_partial('guard/form_register', array('form' => $form)) ?>
</div>