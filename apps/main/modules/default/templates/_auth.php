<!-- Registration -->

<div class="popup" id="auth-block">
  <i title="Закрыть" class="close">Закрыть</i>
  <h2 class="pouptitle">Вход в Enter</h2>
  <?php include_component('guard', 'form_auth') ?>
  <!--
  <div class="shareline">
    <div class="fl">Войти на сайт через:</div>
    <?php //include_component('guard', 'oauth_links') ?>
    <div class="clear"></div>   
  </div>
  -->
</div>

<!-- /Registration -->


<?php if (false): ?>
<div style="display: none;">
  <div id="auth-form"><?php include_component('guard', 'form_signin') ?></div>
</div>
<?php endif ?>