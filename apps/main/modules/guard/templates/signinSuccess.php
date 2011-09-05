<?php slot('form_signin', ' ') ?>

<h1>Авторизация</h1>

<div class="block medium">
  <?php echo get_partial('guard/form_signin', array('form' => $form)) ?>
</div>