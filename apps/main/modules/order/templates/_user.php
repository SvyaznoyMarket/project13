<div id="user-block">
<?php if ($sf_user->isAuthenticated()): ?>
  Привет, <a href="<?php echo url_for('user') ?>"><?php echo $sf_user->getGuardUser() ?></a>
<?php else: ?>
  Уже покупали у нас? <a class="auth-link" data-update-url="<?php echo url_for('order_getUser') ?>" href="<?php echo url_for('user_signin') ?>">Авторизуйтесь</a> и вы сможете использовать ранее введенные данные
<?php endif ?>
</div>