<?php
/**
 * @var $page \View\Layout
 */
?>

<script type="text/html" id="auth_tmpl">
    <a href="<?= $page->url('user') ?>"><%=user%> </a>
    &nbsp;<a id="bUserlogoutLink" href="<?= $page->url('user.logout') ?>">(выйти)</a>
</script>

<a id="auth-link" href="<?= $page->url('user.login') ?>">Войти</a>
