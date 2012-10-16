<script type="text/html" id="auth_tmpl">
	<a href="<?php echo url_for('user') ?>"><%=user%></a>
	&nbsp;<a href="<?php echo url_for('user_signout') ?>">(выйти)</a>
</script>

<a id="auth-link" href="<?php echo url_for('user_signin') ?>">Войти на сайт</a>
