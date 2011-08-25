<div class="right">
  <ul class="inline">
  <?php if ($sf_user->isAuthenticated()): ?>
    <li>
      <a href="<?php echo url_for('user') ?>"><?php echo $sf_user->getGuardUser() ?></a>
    </li>
    <li>
      <a href="<?php echo url_for('user_signout') ?>">Выйти</a>
    </li>
    <?php else: ?>
      <a href="<?php echo url_for('user_signin') ?>">Войти</a>
    <?php endif ?>
  </ul>
</div>