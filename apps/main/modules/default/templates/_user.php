<?php if ($sf_user->isAuthenticated()): ?>
  <a href="<?php echo url_for('user') ?>"><?php echo $sf_user->getGuardUser() ?></a>&nbsp;<a href="<?php echo url_for('user_signout') ?>">(выйти)</a>
<?php else: ?>
  <a id="auth-link" href="<?php echo url_for('user_signin') ?>" class="entry">Войти на сайт</a>
<?php endif ?>