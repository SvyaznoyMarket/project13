<?php slot('title', 'Регистрация') ?>

<?php slot('form_signin', ' ') ?>

<div class="fl">
  <?php echo get_partial('guard/form_register', array('form' => $form)) ?>
</div>