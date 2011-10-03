<?php slot('form_signin', ' ') ?>

<?php slot('title', 'Авторизация') ?>

<div class="block medium">
  <?php echo get_partial('guard/form_signin', array('form' => $form)) ?>
</div>