<script type="text/html" id="auth_tmpl">
	<a href="<?php echo $this->url('user.index') ?>"><%=user%> </a>
	&nbsp;<a href="<?php echo $this->url('user.logout') ?>">(выйти)</a>
</script>

<a id="auth-link" href="<?php echo $this->url('user.signin') ?>">Войти на сайт</a>
